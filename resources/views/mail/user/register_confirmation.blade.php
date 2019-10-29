@component('mail::message')
# Registration confirmation

Thank you for registration on site {{ config('app.url') }}

**Your credentials are:**

@component('mail::panel')
<ul>
    <li><b>Email: </b> {{ $email }}</li>
    <li><b>Password: </b> {{ $password }}</li>
</ul>
@endcomponent

@component('mail::button', ['url' => config('app.url')])
Visit site
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
