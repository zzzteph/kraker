@include('include.header')
  <section class="section">
	<div class="container">
	<table class="table is-fullwidth">
	  <thead>
		<tr>

		  <th><abbr title="IP">Name</abbr></th>
		  <th><abbr title="Type">Type</abbr></th>
    <th><abbr title="Status">Status</abbr></th>
		  <th><abbr title="Action">Cracked</abbr></th>
        <th></th>
		</tr>
	  </thead>
	
		<tbody>
	@foreach ($hashlists as $hashlist)
	<tr>

		<td><a href="/hashlists/{{$hashlist->id}}">{{$hashlist->name}}</a></td>

		<td>{{$hashlist->hashtype_name}}</td>
   <td>
   @if ($hashlist->status === 'todo')
<span class="tag is-warning">{{$hashlist->status}}</span>
    
   @elseif ($hashlist->status === 'valid')
    <span class="tag is-success">{{$hashlist->status}}</span>
@else
    <span class="tag is-danger">{{$hashlist->status}}</span>
@endif
   
   </td>
    <td>{{$hashlist->cracked_count}} / {{$hashlist->count}} <a href="/hashlists/{{$hashlist->id}}/cracked"> <i class="fas fa-download"></i></a></td>
	<td>
  <form method="POST"  enctype="multipart/form-data" action="/hashlists/{{$hashlist->id}}">
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