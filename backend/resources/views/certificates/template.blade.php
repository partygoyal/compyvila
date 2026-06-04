<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            padding-top: 120px;
        }

        h1 {
            font-size: 42px;
        }

        h2 {
            font-size: 34px;
            margin-top: 40px;
        }

        p {
            font-size: 18px;
        }
    </style>
</head>

<body>

    <h1>Certificate of Achievement</h1>

    <p>This certificate is proudly awarded to</p>

    <h2>
        {{ $certificate->user->name }}
    </h2>

    <p>
        for participation in
    </p>

    <h2>
        {{ $certificate->competition->title }}
    </h2>

    <br>

    <p>
        Certificate No:
        {{ $certificate->certificate_number }}
    </p>

    <p>
        Issued:
        {{ $certificate->issued_at }}
    </p>

</body>

</html>
