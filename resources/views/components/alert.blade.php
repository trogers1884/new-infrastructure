@props(['type' => 'info', 'message'])

@if($message)
    <div {{ $attributes->merge([
        'class' => 'px-4 py-3 rounded relative mb-4 ' .
        ($type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
         ($type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
          'bg-blue-100 border border-blue-400 text-blue-700'))
    ]) }}>
        {{ $message }}
    </div>
@endif
