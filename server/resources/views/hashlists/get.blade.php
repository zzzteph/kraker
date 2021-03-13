@include('include.header')
  <section class="section">
	<div class="container">
   <h1  class="title is-1"> {{$hashlist->name}}
   
   @if ($hashlist->status === 'todo')
<span class="tag is-warning">{{$hashlist->status}}</span>
    
   @elseif ($hashlist->status === 'valid')
    <span class="tag is-success">{{$hashlist->status}}</span>
@else
    <span class="tag is-danger">{{$hashlist->status}}</span>
@endif
  
   
   
   
   <h2 class="subtitle">{{$hashlist->hashtype_name}}  <a href="/hashlists/{{$hashlist->id}}/source"><i class="fas fa-download"></i></a></h2>
	<h2 class="subtitle">Progress: {{$hashlist->cracked_count}} / {{$hashlist->count}} </h2>


	</div>
  </section>


  <section class="section">
	<div class="container">
  <h2 class="subtitle">Cracked <a href="/hashlists/{{$hashlist->id}}/cracked"> <i class="fas fa-download"></i></a></h2>
	<table class="table is-bordered">
	@foreach ($hashlist->pot_data as $pot)
    	<tr><td>{{$pot}}</td></tr>
@endforeach



	</table>
	</div>
  </section>


@include('include.footer')