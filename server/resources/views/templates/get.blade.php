@include('include.header')
  <section class="section">
	<div class="container">
   <h1  class="title is-1"> {{$template->name}}
   
		   @if ($template->status === 'todo')
		<span class="tag is-warning">{{$template->status}}</span>
			
		   @elseif ($template->status === 'valid')
			<span class="tag is-success">{{$template->status}}</span>
		@else
			<span class="tag is-danger">{{$template->status}}</span>
		@endif
  
   
   
   </h1>
   <h2 class="subtitle">Keyspace: {{$template->keyspace}}</h2>

	<div class="card">
		  <header class="card-header">
			<p class="card-header-title">
			<strong>{{ucfirst($template->type)}}</strong>
			</p>
		  </header>
  
    <div class="card-content">
    <div class="content">
		 @if ($template->type === 'mask')
			<div class="block"> <strong>Mask:</strong> {{$template->content->mask}}</div>
			@if ($template->content->charset1 !== null)
					<div class="block"> <strong>Charset 1:</strong> {{$template->content->charset1}}</div>
			@endif
			@if ($template->content->charset2 !== null)
					<div class="block"> <strong>Charset 2:</strong> {{$template->content->charset2}}</div>
			@endif
			@if ($template->content->charset3 !== null)
					<div class="block"> <strong>Charset 3:</strong> {{$template->content->charset3}}</div>
			@endif
			@if ($template->content->charset4 !== null)
					<div class="block"> <strong>Charset 4:</strong> {{$template->content->charset4}}</div>
			@endif


		@elseif ($template->type === 'wordlist')
			<div class="block"> <strong>Wordlist:</strong> <a href="/inventory/">{{$template->content->wordlist->name}}</a></div>
			@if ($template->content->rule !== null)
				<div class="block"> <strong>Rule:</strong> <a href="/inventory/">{{$template->content->rule->name}}</a></div>
			@endif


		@elseif ($template->type === 'chain')
						<table class="table table is-fullwidth">
						  <thead>
							<tr>
							  <th><abbr title="id">#</abbr></th>
							  <th><abbr title="Name">Name</abbr></th>
							  <th><abbr title="Type">Type</abbr></th>
							  <th><abbr title="Status">Status</abbr></th>
							  <th><abbr title="Keyspace">Keyspace</abbr></th>
							</tr>
						  </thead>
						
							<tbody>
			@foreach ($template->content as $chain)

						<tr>
							<td><a href="/templates/{{$chain->template->id}}">{{$chain->template->id}}</td>
							<td>{{$chain->template->name}}</td>

							
							<td>{{$chain->template->type}}</td>
							
									<td>
							   @if ($chain->template->status === 'todo')
					<span class="tag is-warning">{{$chain->template->status}}</span>
						
					   @elseif ($chain->template->status === 'valid')
						<span class="tag is-success">{{$chain->template->status}}</span>
					@else
						<span class="tag is-danger">{{$chain->template->status}}</span>
					@endif

							</td>
							
							
							
						<td>{{$chain->template->keyspace}}</td>
						 
						</tr>
			
			@endforeach

		</tbody>
						</table>

		@endif


    </div>
  </div>
  
  
</div>

	</div>
  </section>





@include('include.footer')