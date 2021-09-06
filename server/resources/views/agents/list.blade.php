@include('include.header')
  <section class="section">
	<div class="container">
	<table class="table table is-fullwidth">
	  <thead>
		<tr>
		  <th><abbr title="id">#</abbr></th>
		  <th><abbr title="IP">IP</abbr></th>
		  <th><abbr title="Last seen">LS</abbr></th>
		  <th><abbr title="Last action">LA</abbr></th>
        <th></th>
		<th></th>
        <th></th>
		</tr>
	  </thead>
	
		<tbody>
	@foreach ($agents as $agent)
	
	@if ($agent->enabled==1)
	<tr class="has-background-success-light">
	@else
	<tr>
	 @endif
		<td><a href="/agents/{{$agent->id}}">{{$agent->hostname}} ({{$agent->ip}})</td>
		<td>{{$agent->ip}}</td>
		<td> 
    @if ($agent->last_seen <60)
      <span class="tag is-success">
    @elseif ($agent->last_seen >= 60 && $agent->last_seen < 600)
      <span class="tag is-warning">
    @else
      <span class="tag is-danger">
    @endif
    {{$agent->last_seen}}
    </span>
    
    </td>
		<td>
		{{$agent->latest_action}}
		
		</td>

	
 <td>
 
    <form method="POST" action="/agents/{{$agent->id}}">
    @csrf
    @method('PUT')
      @if ($agent->enabled==0)
    <input type="hidden" name="enabled" value="1">
               @else
      <input type="hidden" name="enabled" value="0">
    @endif
    <div class="field">
        <div class="control">
          @if ($agent->enabled==0)
          <button class="button is-success is-small">Enable</button>
            @else
      <button class="button is-danger is-small">Disable</button>
    @endif
        </div>

      </div>
    
    
  </form>
 
 
 
 
 
 </td>
 
 
  <td>
  
      <form method="POST" action="/agents/{{$agent->id}}/reset">
    @csrf
    @method('PUT')

    <div class="field">
        <div class="control">

			<button class="button is-danger is-small">Reset</button>

        </div>

      </div>
    
    
  </form>
  
  
  
  </td>
 
 
 <td>
 
 
   <form method="POST" action="/agents/{{$agent->id}}">
    @csrf
    @method('DELETE')
    <div class="field">
        <div class="control">
          <button class="button is-danger is-small">Delete</button>
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