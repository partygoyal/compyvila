<label class="tool-field">{{ $field['label'] }}
@if($field['kind'] === 'select')<select name="{{ $field['key'] }}">@foreach($field['options'] as $value=>$label)<option value="{{ $value }}" @selected((string)$value === (string)$field['value'])>{{ $label }}</option>@endforeach</select>
@else<input name="{{ $field['key'] }}" type="{{ $field['kind'] }}" value="{{ $field['value'] }}" @if($field['kind']==='number') min="0" step="any" inputmode="decimal" @elseif($field['key']==='amount') inputmode="decimal" maxlength="100" @else maxlength="80" @endif required autocomplete="off">@endif
</label>
