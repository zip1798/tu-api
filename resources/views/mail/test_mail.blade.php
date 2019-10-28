@component('mail::message')
# Introduction

The body of your message. {{ $msg }}

@component('mail::panel')
This is the panel content.
@endcomponent

![product](https://tu-dc-test.gmhost.space/media/event/main/m-18-99f2ccab8c67af941792b657da3a3f9d.jpg)

@component('mail::table')
| Laravel       | Table         | Example  |
| ------------- |:-------------:| --------:|
| Col 2 is      | Centered      | $10      |
| Col 3 is      | Right-Aligned | $20      |
@endcomponent

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
