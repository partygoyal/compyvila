<label>{{ $label }}
    <select name="{{ $name }}">
        @foreach($options as $option)
            <option value="{{ $option }}" @selected($option === old($name, $value))>{{ ucfirst($option) }}</option>
        @endforeach
    </select>
</label>
