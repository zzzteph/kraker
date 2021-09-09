<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KRAKER</title>
	 <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bulma.min.css')}}">
	<link href="{{ asset('fontawesome/css/all.css')}}" rel="stylesheet">
	<link href="{{ asset('css/selectize.min.css')}}" rel="stylesheet">
	<script src="{{ asset('fontawesome/js/all.js')}}"></script>
    <script src="{{ asset('js/jquery-3.5.1.js')}}"></script>
    <script src="{{ asset('js/vue.js')}}"></script>
	<script src="{{ asset('js/axios.min.js')}}"></script>
    <script src="{{ asset('js/selectize.min.js')}}"></script>
	    <style type="text/css" media="screen">
      body {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
      }

      #wrapper {
        flex: 1;
      }
    </style>
  </head>
  <body>



    <section class="section">
    <div class="container">
  <nav class="navbar" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
    <a class="navbar-item" href="/">
      <img src="/cracker.png">
    </a>

    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>

   <div id="navbarBasicExample" class="navbar-menu">
    <div class="navbar-start">
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="/">
          Task
        </a>

        <div class="navbar-dropdown">
          <a class="navbar-item" href="/">
           Current tasks
          </a>
          <hr class="navbar-divider">
          <a class="navbar-item" href="/tasks">
             All tasks
          </a>
        </div>
      </div>



      <a class="navbar-item" href="/agents">
        Agents
      </a>

      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="/templates">
          Templates
        </a>

        <div class="navbar-dropdown">
          <a class="navbar-item" href="/templates">
           All templates
          </a>
          <hr class="navbar-divider">
          <a class="navbar-item" href="/templates/new">
            Add new template
          </a>
        </div>
      </div>


      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="/hashlists">
          Hashlists
        </a>

        <div class="navbar-dropdown">
          <a class="navbar-item" href="/hashlists">
           All hashlists
          </a>
          <hr class="navbar-divider">
          <a class="navbar-item" href="/hashlists/new">
            Add new hashlist
          </a>
        </div>
      </div>



      <a class="navbar-item" href="/inventory">
        Inventory
      </a>
	  
	  
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link" href="/hashtypes">
          Settings
        </a>

        <div class="navbar-dropdown">
          <a class="navbar-item" href="/hashtypes">
           Hashtypes
          </a>
       
		  
		    <hr class="navbar-divider">
		            <a class="navbar-item" href="/notifications/telegram">
            <i class="fab fa-telegram"></i>&nbsp;Telegram
          </a>
        </div>
      </div>

    </div>

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <a class="button is-primary" href="/tasks/new">
            <strong>New task</strong>
          </a>
		  @if(Auth::check())
		   <a class="button is-danger" href="/logout">
            Log out
          </a> 
		  @else 
          <a class="button is-light" href="/login">
            Log in
          </a>
        </div>

		@endif 



      </div>
    </div>
  </div>
</nav>
</div>
</section>
<div id="wrapper">