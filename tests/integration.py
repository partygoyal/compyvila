"""Exercise the real PHP application over HTTP against an isolated database."""
import http.cookiejar
import secrets
import base64
import os
from pathlib import Path
import re
import socket
import sqlite3
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
checks = 0


def check(condition, message):
    global checks
    assert condition, message
    checks += 1


with tempfile.TemporaryDirectory(prefix='compyvila-test-') as temp:
    database = str(Path(temp) / 'test.sqlite')
    env = {**os.environ, 'DB_CONNECTION': 'sqlite', 'DB_DATABASE': database, 'DB_SOCKET': '', 'APP_ENV': 'testing-http', 'CACHE_FILE_PATH': str(Path(temp) / 'cache'), 'SESSION_FILES_PATH': str(Path(temp) / 'sessions')}
    Path(database).touch()
    (Path(temp) / 'sessions').mkdir()
    (Path(temp) / 'cache').mkdir()
    subprocess.run(['php', 'artisan', 'migrate', '--seed', '--force'], cwd=ROOT, env=env, check=True, capture_output=True)
    subprocess.run(['php', 'artisan', 'migrate', '--seed', '--force'], cwd=ROOT, env=env, check=True, capture_output=True)
    created = subprocess.run(['php', 'artisan', 'compyvila:create-admin', 'test@example.com'], cwd=ROOT, env=env, check=True, capture_output=True, text=True).stdout
    password = re.search(r'Password: (\S+)', created).group(1)
    with socket.socket() as available:
        available.bind(('127.0.0.1', 0))
        port = available.getsockname()[1]
    origin = f'http://127.0.0.1:{port}'
    log = open(Path(temp) / 'server.log', 'w+')
    server = subprocess.Popen(['php', '-S', f'127.0.0.1:{port}', '-t', 'public', 'public/router.php'], cwd=ROOT, env=env, stdout=log, stderr=log)
    client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))

    def request(path, fields=None, opener=client):
        data = urllib.parse.urlencode(fields).encode() if fields is not None else None
        try:
            result = opener.open(origin + path, data=data, timeout=5)
        except urllib.error.HTTPError as result_error:
            result = result_error
        return result.status, result.read().decode(), result.headers

    def token(body):
        return re.search(r'name="_token" value="([^"]+)"', body).group(1)

    uploaded_path = None
    try:
        for _ in range(40):
            try:
                status, home, _ = request('/')
                break
            except urllib.error.URLError:
                time.sleep(.1)
        else:
            raise AssertionError('Server did not start')
        check(status == 200 and 'Crypto, with' in home, 'Homepage renders')
        check('PREVIEW EDITION' in home and 'ILLUSTRATIVE' in home, 'Samples labelled')
        check('Authors' not in home and 'href="/about"' not in home, 'No author or About links')
        db = sqlite3.connect(database)
        check(db.execute('SELECT COUNT(*) FROM articles').fetchone()[0] == 10, 'Installer is repeatable')
        for path in ['/news', '/analysis', '/bitcoin', '/ethereum', '/altcoins', '/learn', '/calendar', '/media-kit', '/market-notes', '/contact', '/editorial-policy', '/privacy', '/terms', '/watchlist', '/search', '/sitemap.xml']:
            status, body, headers = request(path)
            check(status == 200 and 'Fatal error' not in body, f'{path} renders')
            if path != '/sitemap.xml':
                check('<meta name="viewport"' in body, f'{path} has viewport')
        status, kit, headers = request('/media-kit/download')
        check(status == 200 and 'attachment' in headers.get('Content-Disposition', ''), 'Downloadable media kit')
        check('No audience figures are claimed' in kit, 'Kit has no fabricated audience')
        for url in sorted(set(re.findall(r'(?:href|src)="([^"#]+)', home))):
            if url.startswith('/'):
                status, body, headers = request(url)
                check(status == 200, f'Home link or asset resolves: {url}')
        for path in ['/missing-page', '/read/absent-story', '/.env', '/storage/admin-access.txt']:
            check(request(path)[0] == 404, f'{path} is not exposed')
        status, search, _ = request('/search?q=wallet')
        check('Your first wallet' in search, 'Search finds body and headline matches')
        status, search, _ = request('/search?q=zzzznotfound')
        check('No stories found.' in search, 'Empty search state')
        status, search, _ = request('/search?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E')
        check('<script>alert(1)</script>' not in search, 'Search input is escaped')
        check(request('/subscribe', {'email': 'reader@example.com'})[0] == 419, 'CSRF blocks untrusted form')
        csrf = token(request('/')[1])
        status, body, _ = request('/subscribe', {'_token': csrf, 'email': 'bad-email', 'consent': '1'})
        check(db.execute('SELECT COUNT(*) FROM subscribers').fetchone()[0] == 0, 'Invalid email rejected')
        for _ in range(2):
            request('/subscribe', {'_token': csrf, 'email': 'reader@example.com', 'consent': '1'})
        check(db.execute('SELECT COUNT(*) FROM subscribers').fetchone()[0] == 1, 'Subscriptions persist and deduplicate')
        request('/media-enquiry', {'_token': csrf, 'name': 'A Reader', 'email': 'media@example.com', 'company': 'Example', 'placement': 'Display placement', 'message': 'Please share campaign availability.'})
        check(db.execute('SELECT COUNT(*) FROM enquiries').fetchone()[0] == 1, 'Enquiry persists')
        check('The editorial desk.' in request('/admin?tab=subscribers')[1], 'Subscriber list requires login')
        check(request('/admin/export')[2].get('Content-Type', '').startswith('text/html'), 'Export requires login')
        status, login, _ = request('/admin')
        csrf = token(login)
        status, login, _ = request('/admin/login', {'_token': csrf, 'email': 'test@example.com', 'password': 'wrong'})
        check('not recognised' in login, 'Bad password rejected')
        status, desk, _ = request('/admin/login', {'_token': csrf, 'email': 'test@example.com', 'password': password})
        check('Welcome to the desk.' in desk, 'Admin signs in')
        csrf = token(desk)
        for tab in ['articles', 'market', 'events', 'subscribers', 'enquiries', 'settings']:
            status, body, _ = request('/admin?tab=' + tab)
            check(status == 200 and 'Fatal error' not in body, 'Admin tab ' + tab)
        fields = {'_token': csrf, 'action': 'save-article', 'id': '0', 'title': 'Integration research story', 'slug': 'integration-research', 'excerpt': 'A useful integration test for our editorial workflow.', 'body': '## Evidence first\n\nA research note uses primary sources and dates to explain its reasoning.', 'category': 'Analysis', 'coin': 'Bitcoin', 'sources': 'https://bitcoin.org/', 'status': 'draft', 'published_at': '2026-10-01T10:00'}
        request('/admin/article', fields)
        article_id = db.execute('SELECT id FROM articles WHERE slug=?', ('integration-research',)).fetchone()[0]
        check(request('/read/integration-research')[0] == 404, 'Draft is private')
        fields.update(id=str(article_id), status='published', published_at='2099-01-01T10:00')
        request('/admin/article', fields)
        check(request('/read/integration-research')[0] == 404, 'Future story is private')
        fields.update(published_at='2026-10-01T10:00')
        request('/admin/article', fields)
        status, story, _ = request('/read/integration-research')
        check(status == 200 and '<h2>Evidence first</h2>' in story, 'Published article renders body headings')
        fields.update(sources='javascript:alert(1)')
        status, body, _ = request('/admin/article', fields)
        check('Each source must be' in body, 'Unsafe source URL rejected')
        check(db.execute('SELECT sources FROM articles WHERE id=?', (article_id,)).fetchone()[0] == 'https://bitcoin.org/', 'Invalid edit does not replace saved article')
        fields.update(sources='https://bitcoin.org/')

        def upload(filename, mime, payload):
            boundary = 'CompyvilaIntegrationBoundary'
            parts = []
            for key, value in fields.items():
                parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="{key}"\r\n\r\n{value}\r\n'.encode())
            parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="image"; filename="{filename}"\r\nContent-Type: {mime}\r\n\r\n'.encode() + payload + b'\r\n')
            parts.append(f'--{boundary}--\r\n'.encode())
            req = urllib.request.Request(origin + '/admin/article', data=b''.join(parts), headers={'Content-Type': f'multipart/form-data; boundary={boundary}'})
            return client.open(req).read().decode()

        check('The image' in upload('malicious.php', 'image/png', b'<?php echo "no";'), 'Disguised executable upload rejected')
        png = base64.b64decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aELsAAAAASUVORK5CYII=')
        upload('chart.png', 'image/png', png)
        image_url = db.execute('SELECT image FROM articles WHERE id=?', (article_id,)).fetchone()[0]
        uploaded_path = ROOT / 'public' / image_url.lstrip('/')
        check(image_url.startswith('/uploads/') and uploaded_path.is_file(), 'Valid chart upload saved under generated filename')
        check(image_url in request('/read/integration-research')[1], 'Uploaded image appears on article')
        request('/admin/event', {'_token': csrf, 'action': 'save-event', 'title': 'Test event', 'description': 'An isolated calendar event for verification.', 'category': 'Upgrade', 'event_at': '2099-01-01T12:00', 'source': 'https://ethereum.org/'})
        check('Test event' in request('/calendar?category=Upgrade')[1], 'Event create and category filter')
        request('/admin/snapshot', {'_token': csrf, 'action': 'save-snapshot', 'symbol': 'BTC', 'name': 'Bitcoin', 'price': '123.45', 'change_pct': '-1.25', 'captured_at': '2026-10-14T10:30', 'source': 'https://bitcoin.org/'})
        check('$123.45' in request('/bitcoin')[1], 'Decimal snapshot update')
        request('/admin/settings', {'_token': csrf, 'action': 'settings', 'brief_title': 'Our test briefing', 'brief_body': 'This saved briefing proves the editorial workflow.', 'brief_points': 'First point\nSecond point\nThird point', 'media_audience': 'Independent readers.', 'media_email': 'hello@example.com', 'demo': 'on'})
        check('Our test briefing' in request('/')[1], 'Briefing settings persist')
        check('hello@example.com' in request('/media-kit')[1], 'Media contact settings persist')
        status, export, headers = request('/admin/export')
        check(status == 200 and 'reader@example.com' in export and 'text/csv' in headers['Content-Type'], 'Subscriber export')
        request('/admin/records/articles/' + str(article_id), {'_token': csrf, '_method': 'DELETE'})
        check(request('/read/integration-research')[0] == 404, 'Article deletion')
        _, changed, _ = request('/admin/password', {'_token': csrf, 'action': 'password', 'current_password': password, 'new_password': 'a-new-test-password-123'})
        check('Password changed.' in changed, 'Password change handled')
        csrf = token(changed)
        request('/admin/logout', {'_token': csrf})
        check('The editorial desk.' in request('/admin')[1], 'Sign out')
        csrf = token(request('/admin')[1])
        _, signed_in, _ = request('/admin/login', {'_token': csrf, 'email': 'test@example.com', 'password': 'a-new-test-password-123'})
        check('Welcome to the desk.' in signed_in, 'New password authenticates')
        request('/admin/logout', {'_token': token(signed_in)})
        csrf = token(request('/admin')[1])
        for _ in range(6):
            _, body, _ = request('/admin/login', {'_token': csrf, 'email': 'test@example.com', 'password': 'wrong'})
        check('Too many sign-in attempts' in body, 'Login throttle')
        db.close()
        print(f'PASS: {checks} integration checks; isolated database removed.')
    finally:
        if uploaded_path and uploaded_path.is_file():
            uploaded_path.unlink()
        server.terminate()
        server.wait(timeout=5)
        log.seek(0)
        errors = [line for line in log if 'Fatal error' in line or 'Warning:' in line]
        log.close()
        if errors:
            raise AssertionError('PHP runtime errors: ' + ''.join(errors))
