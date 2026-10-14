<form method="post" action="{{ route('admin.records.destroy', ['type' => $table, 'id' => $id]) }}" data-confirm="Permanently delete this record?">
    @csrf
    @method('DELETE')
    <button class="delete-button">Delete</button>
</form>
