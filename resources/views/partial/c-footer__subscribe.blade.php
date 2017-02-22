<footer class="[ c-footer ]">

<div class="[ grid ]">

    <div class="[ c-footer__item ] [ grid__item one-whole ]">

    {{ Form::open(array('class' => '[ s-form-subscribe ] [ js--subscribe__form ]', 'url' => Config::get('services.mailchimp.long'))) }}

    <div class="[ grid ] [ grid--narrow ]">

           <div class="[ grid__item desk--two-thirds ]">
               {{ Form::email('email', null, ['placeholder' => 'Email', 'class' => '[ js--subscribe__email ] [ u-case-input-lower ]', 'name' => 'EMAIL']) }}
           </div>
           <div class="[ grid__item desk--one-third ]">
               {{ Form::submit(trans('space::base.subscribe'), ['class' => '[ js--subscribe__submit ] [ button--background-white ]']) }}
           </div>

    </div>

    {{ Form::close() }}

       <p class="[ js--subscribe__label ]{{--
             --}}[ u-font-size--a u-opacity--half u-select-none ]{{--
             --}}[ u-text-align--portable-center ]">
           {{ trans('space::base.receive-our-posts') }}
       </p>

    </div>

</div>
</footer>
