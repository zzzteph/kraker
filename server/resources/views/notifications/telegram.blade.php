@include('include.header')
  <section class="section">
	<div class="container">
	
	<form method="POST" action="/notifications/telegram">
    @csrf

		<div class="field">
		  <label class="label">Bot token</label>
		  <div class="control">
		@isset($telegram)
			<input class="input" type="text" name="token" value="{{$telegram->token}}">
			@else
				<input class="input" type="text" name="token">
			@endisset
		  </div>
		</div>
		<div class="field">
		  <label class="label">Chat id</label>
		  <div class="control">
		  @isset($telegram)
			<input class="input" type="text" name="chat" value="{{$telegram->chat_id}}">
			@else
			
			<input class="input" type="text" name="chat" value="">
			@endisset
		  </div>
		</div>

		

		<div class="field">
		  <div class="control">
			<label class="radio">
			 @isset($telegram)
			 
			@if ($telegram->enabled == 1)

			  <input type="radio" name="enabled" value="true" checked>
			  Yes
			</label>
			<label class="radio">
			  <input type="radio" name="enabled" value="false" >
			  No
			</label>
			@else
						  <input type="radio" name="enabled" value="true">
			  Yes
			</label>
			<label class="radio">
			  <input type="radio" name="enabled" checked value="false">
			  No
			</label>
			@endif
			@else
			<input type="radio" name="enabled" value="true">
			  Yes
			</label>
			<label class="radio">
			  <input type="radio" name="enabled" checked value="false">
			  No
			</label>
			@endisset
			
		  </div>
		</div>

		<div class="field is-grouped">
		  <div class="control">
			<button class="button is-link">Submit</button>
		  </div>
		</div>
</form>




	</div>
  </section>




@include('include.footer')
