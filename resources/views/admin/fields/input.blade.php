<label>{{ $label }}
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $type === 'password' ? '' : old($name, $value) }}" @required($required) @if($type === 'number') step="any" @endif>
</label>
