@include('include.header')
  <section class="section">
	<div class="container">
   <h1  class="title is-1"> {{$task->id}}
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
  
  
   
   
   </h1>
   <progress class="progress is-primary" value="{{$task->progress}}" max="100">{{$task->progress}}%</progress>

  <h2 class="subtitle"> Hashlist: <a href="/hashlists/{{$task->hashlist_id}}">{{$task->hashlist->name}}</a></h2>
  
    <h2 class="subtitle"> Template: <a href="/templates/{{$task->template_id}}">{{$task->template->name}}</a></h2>

	<table class="table is-bordered is-fullwidth">
	<thead>
		<tr>
			<th>#</th>
			<th>Agent</th>
			<th>Errors</th>
			<th>Cracked</th>
			
		</tr>
	</thead>
	@foreach ($task->jobs as $job)
	
	@if ($job->status == 'running')
		  <tr class="has-background-info-light">
		@elseif ($job->status == 'done')
		  <tr class="has-background-success-light">
		@elseif ($job->status == 'error')
		 <tr class="has-background-danger-light">
		@elseif ($job->status == 'todo')
			 
			<tr>
	@endif 

			<td>{{$job->id}}
			
				@if ($job->status == 'running')
		 <i class="fas fa-sync fa-spin"></i>
		@elseif ($job->status == 'done')
		<i class="fas fa-check-square"></i>
		@elseif ($job->status == 'error')
	<i class="fas fa-exclamation-triangle"></i>

	@endif 
			
			</td>
			<td>
			@if ($job->status !== null)
			<a href="/agents/{{$job->agent_id}}">{{$job->agent_id}}
			
			@else
			
			@endif 
			</td>
			<td>{{$job->errors}}</td>
			<td>{{$job->cracked}}</td>
		</tr>
@endforeach



	</table>
	</div>
  </section>


@include('include.footer')