@include('include.header')
  <section class="section">
	<div class="container">
	
	<table class="table is-fullwidth">
		<thead>
		<tr>
			<th>#</th>
			<th>Template</th>
			<th>Hashlist</th>
			<th>Status</th>
			<th>Cracked</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		@foreach ($tasks as $task)
		<tr>
			<td><a href="/tasks/{{$task->id}}">{{$task->id}}</a></td>
			<td><a href="/templates/{{$task->template->id}}">{{$task->template->name}}</a></td>
			<td><a href="/hashlists/{{$task->hashlist->id}}">{{$task->hashlist->name}}</a></td>
			<td>
			
			  @if ($task->status === 'done')
		<span class="tag is-success">{{$task->status}}</span>
			
		   @elseif ($task->status === 'stopped')
			<span class="tag is-warning">{{$task->status}}</span>
		@elseif ($task->status === 'cancelled')

		<span class="tag is-danger">{{$task->status}}</span>
		@elseif ($task->status === 'error')
			<span class="tag is-danger">{{$task->status}}</span>
		@elseif ($task->status === 'todo')
			<span class="tag is-info">{{$task->status}}</span>
		@endif
			
			
			
			</td>
			<td>{{$task->cracked}}</td>
			<td>
			 @if ($task->status === 'stopped')
			  <form method="POST"  enctype="multipart/form-data" action="/tasks/{{$task->id}}/start">
				@csrf
				@method('PUT')
				<div class="field">
					<div class="control">
					  <button class="button is-success is-small">Start</button>
					</div>

				  </div>
				
				
			  </form>
			@endif		
			
			
			 @if ($task->status === 'todo')
			  <form method="POST"  enctype="multipart/form-data" action="/tasks/{{$task->id}}/stop">
				@csrf
				@method('PUT')
				<div class="field">
					<div class="control">
					  <button class="button is-danger is-small">Stop</button>
					</div>

				  </div>
				
				
			  </form>
			@endif		
			
			
			
			
			</td>
		
		</tr>
		@endforeach
		</tbody>
	</table>
	
	
	
	{{ $tasks->links() }}
	</div>
  </section>





@include('include.footer')