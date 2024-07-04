@component('mail::message')
# Please be aware that the following documents are about to expire or have already expired:

@component('mail::panel')
<ul>
    @foreach($expiringDocuments as $document)
    <li>{{ $document->name }} - {{ $document->expiryForHumans }}</li>
    @endforeach
</ul>
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent