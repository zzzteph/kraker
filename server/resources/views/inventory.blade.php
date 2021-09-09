@include('include.header')
  <section class="section">
	<div class="container">
	<h1 class="title">Wordlists</h1>
	<div class="table-container">
	<table class="table table is-fullwidth">
	  <thead>
		<tr>
		  <th><abbr title="Name">Name</abbr></th>
		  <th><abbr title="Words Count">Words count</abbr></th>
		  <th><abbr title="agents">Agents</abbr></th>
        <th></th>
		</tr>
	  </thead>
	
		<tbody>
	@foreach ($inventory as $entry)
	 @if ($entry->type == 'wordlist')

    
	
	
	<tr>
		<td>{{$entry->name}}</td>
		<td>{{$entry->count}}</td>
		<td> 
			@foreach ($entry->agents as $agent)
			
			<a href="/agents/{{$agent->agent_id}}">{{$agent->agent->hostname}} ({{$agent->agent->ip}})</a></br>
			
			@endforeach
		</td>


	
 <td>
 
    <form method="POST"  action="/inventory/{{$entry->id}}">
    @csrf
    @method('DELETE')
  
    <div class="field">
        <div class="control">
			<button class="button is-danger is-small delete-inv">Remove</button>
        </div>
      </div>
    
    
  </form>
 
 
 
 
 
 </td>

 
	 
	</tr>
	@endif
	@endforeach
	</tbody>
	</table>
	</div>
	
	
	<h1 class="title">Rules</h1>
		<div class="table-container">
	<table class="table table is-fullwidth">
	  <thead>
		<tr>
		  <th><abbr title="Name">Name</abbr></th>
		  <th><abbr title="Words Count">Words count</abbr></th>
		  <th><abbr title="agents">Agents</abbr></th>
        <th></th>
		</tr>
	  </thead>
	
		<tbody>
	@foreach ($inventory as $entry)
	 @if ($entry->type == 'rule')

    
	
	
	<tr>
		<td>{{$entry->name}}</td>
		<td>{{$entry->count}}</td>
		<td> 
			@foreach ($entry->agents as $agent)
			
			<a href="/agents/{{$agent->agent_id}}">{{$agent->agent_id}}</a></br>
			
			@endforeach
		</td>


	
 <td>
 
    <form method="POST"  action="/inventory/{{$entry->id}}">
    @csrf
    @method('DELETE')
  
    <div class="field">
        <div class="control">
			<button class="button is-danger is-small delete-inv">Remove</button>
        </div>
      </div>
    
    
  </form>
 
 
 
 
 
 </td>

 
	 
	</tr>
	@endif
	@endforeach
	</tbody>
	</table>
	</div>
	
	
	
	</div>
	
	
	
	
	
  </section>
<script>
    $('.delete-inv').click(function(e){
        e.preventDefault()
        if (confirm('Are you really want to remove this item?')) {
            $(e.target).closest('form').submit();
        }
    });
</script>




@include('include.footer')