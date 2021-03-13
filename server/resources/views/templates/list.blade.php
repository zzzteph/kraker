@include('include.header')
  <section class="section">
	<div class="container">
	<table class="table table is-fullwidth">
	  <thead>
		<tr>
		  <th><abbr title="id">#</abbr></th>
		  <th><abbr title="Name">Name</abbr></th>
		  <th><abbr title="Type">Type</abbr></th>
		  <th><abbr title="Status">Status</abbr></th>
		  <th><abbr title="Keyspace">Keyspace</abbr></th>
        <th></th>
		</tr>
	  </thead>
	
		<tbody>
	@foreach ($templates as $template)
	<tr>
		<td><a href="/templates/{{$template->id}}">{{$template->id}}</td>
		<td>{{$template->name}}</td>

		
		<td>{{$template->type}}</td>
		
				<td>
		   @if ($template->status === 'todo')
<span class="tag is-warning">{{$template->status}}</span>
    
   @elseif ($template->status === 'valid')
    <span class="tag is-success">{{$template->status}}</span>
@else
    <span class="tag is-danger">{{$template->status}}</span>
@endif
		
		
		
		
		</td>
		
		
		
    <td>{{$template->keyspace}}</td>
	<td>
  <form method="POST"  action="/templates/{{$template->id}}">
    @csrf
    @method('DELETE')
    <div class="field">
        <div class="control">
          <button class="button is-danger">delete</button>
        </div>

      </div>
    
    
  </form>
  
  
  
  </td>
	 
	</tr>
	
	@endforeach
	</tbody>
	</table>
	</div>
  </section>





@include('include.footer')