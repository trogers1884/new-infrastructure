@props(['type' => 'text', 'label', 'name', 'value' => null])

<div class="mb-4">
    @if(isset($label))
        <label class="block text-gray-700 text-sm font-bold mb-2" for="{{ $name }}">
            {{ $label }}
        </label>
    @endif

    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"
        {{ $attributes->merge([
            'class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ' .
            ($errors->has($name) ? 'border-red-500' : '')
        ]) }}>

    @error($name)
    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
    @enderror
</div>
