<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('merchant::form.error')

        <input type="file" class="{{$class}}" name="{{$name}}[]" {!! $attributes !!} />

        @include('merchant::form.help-block')

    </div>
</div>
