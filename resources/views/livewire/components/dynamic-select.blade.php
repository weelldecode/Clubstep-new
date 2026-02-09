<div class="flex flex-col">
    @if($label)
        <label class="text-sm font-medium mb-1">{{ $label }}</label>
    @endif
    <select wire:model="selected" class="border rounded px-2 py-1 w-full">
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</div>
