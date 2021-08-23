@include('include.header')
  <section class="section">
	<div class="container">
   <h1  class="title is-1"> {{$agent->id}}  @if ($agent->last_seen <60)
      <span class="tag is-success">
    @elseif ($agent->last_seen >= 60 && $agent->last_seen < 600)
      <span class="tag is-warning">
    @else
      <span class="tag is-danger">
    @endif
    {{$agent->last_seen}}
    </span></h1>
	
	<div>
	<table class="table">
	<tbody>
		<tr>	<td>Hostname</td><td>{{$agent->hostname}}</td></tr>
		<tr>	<td>IP</td><td>{{$agent->ip}}</td></tr>
		<tr>	<td>hashcat</td><td>{{$agent->hashcat}}</td></tr>
		<tr>	<td>Hardware info</td><td>{!! nl2br(e($agent->hw)) !!}</td></tr>
	</tbody>

	</table>
 </div>
 <div>
 <table class="table">
 <thead>
	<tr>
		<th>Hashtype id</th><th>Speed</th>
	</tr>
 </thead>
 <tbody>
 @foreach ($agent->speed_stats as $speed)
 <tr>	<td>{{$speed->hashtype_id}}</td><td>{{$speed->speed}}</td></tr>
 @endforeach
 </tbody>
 </table>
 </div>
 
  
	</div>
  </section>





@include('include.footer')