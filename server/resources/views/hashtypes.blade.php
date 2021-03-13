@include('include.header')
  <section class="section">
	<div class="container">
	<table class="table table is-fullwidth">
	  <thead>
		<tr>
		  <th><abbr title="id">#</abbr></th>
		  <th><abbr title="Name">Name</abbr></th>
       <th><abbr title="Avg.speed">Avg.speed</abbr></th>
	   
      <th></th>
		</tr>
	  </thead>
	
		<tbody>
	@foreach ($hashtypes as $hashtype)
	
	@if ($hashtype->enabled === 1)
	<tr class="is-selected">
  @else
	  
  <tr>
    @endif
		<td>{{$hashtype->id}}</td>
		<td>{{$hashtype->name}}</td>
    <td>{{$hashtype->avg_speed}}</td>
	<td>
 
 
  <form method="POST"   action="/hashtypes/{{$hashtype->id}}">
    @csrf
	@method('PUT')
    <div class="field">
        <div class="control">
		@if ($hashtype->enabled === 1)
          <button class="button is-danger">Disable</button>
		  @else
			  
		  
		  <button class="button is-primary">Enable</button>
		  <input type="hidden" name="enabled" value="true">
		  @endif
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