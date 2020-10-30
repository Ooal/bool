@extends('layouts.app')
@section('content')




<section class="shadow" style="margin-bottom: 40px ;padding: 25px 0; display: flex;justify-content: center;flex-direction: column;align-items: center;width:100%;background: rgb(131,58,180);background: linear-gradient(90deg, rgba(131,58,180,0.6671043417366946) 0%, rgba(253,29,29,0.7287289915966386) 50%, rgba(252,176,69,0.6502976190476191) 100%);">

    <div class="container h-100 barraRicercaInSearch">
        <div class="d-flex justify-content-center h-100">

            <div class="searchbar">
                <input id="address-input" class="search_input" type="text" name="" placeholder="Search...">
                <a id="bottone" href="{{route('search')}}" class="search_icon"><i class="fas fa-search"></i></a>
            </div>
        </div>
    </div>
    <h5 style="color:#ffff;">Seleziona il raggio di ricerca in km</h5>
    <div style="width: 40vw">
      <input type="range" class="custom-range" id="customRange11" min="0" max="250" value="20">
    </div>
    <span style="color:#ffff;font-size:16px;" class="font-weight-bold  ml-2 valueSpan2"></span>
</section>
<div class="container">
    <div class="row" style="margin-top:10px;">
        <div class="col-md-12 parte-up" style=" display: flex;justify-content: center;flex-direction: column;align-items: center;">
            @if ($city == '')
            <h1 style="display: inline-block;">Tutti i nostri appartamenti</h1>
            @else
            <h1 style="display: inline-block">Appartamenti a {{$city}}</h1>
            @endif



            <div class="servizi-check" style="position:relative; ">
                <ul class="ks-cboxtags">
                    @foreach ($services as $service)
                    <li><input type="checkbox" id="{{$service -> service_name}}" name="service[]" class="form-check-input" id="{{$service -> id}}" value="{{$service -> id}}" rel="{{$service -> service_name}}">
                        <label class="form-check-label" for="{{$service -> service_name}}">{{$service-> service_name}} </label>
                    </li>
                    @endforeach
                </ul>
                <ul class="text-center">
                <li style="display:inline-block;">
                <select id="nofbed" style="display:inline-block;width:100%;border-radius:0px;" name="number_of_bed">
                    @for ($i = 1; $i <= 15; $i++) <option value="{{$i}}">{{$i}} letti</option>
                        @endfor
                </select></li>
                <li style="display:inline-block;">
                <select id="nofroom" style="display:inline-block;width:100%;border-radius:0px;" name="number_of_room">
                    @for ($i = 1; $i <= 15; $i++) <option value="{{$i}}">{{$i}} stanze</option>
                        @endfor
                </select></li></ul>
                <button style="position:absolute;top: 12px;right: -61px;" type="button" id="search-button" class="btn btn-primary btn-sm float-right">Filtra</button>
            </div>
            <span id="riga"></span>
        </div>
    </div>
    <p id="message" class="none text-center">Non ci sono risultati corrispondenti</p>
    <div class="row flatsss-row">
        @foreach($flatselect as $flat)
        @if ($flat -> disactive == 0 && $flat -> deleted == 0)
        <a href="{{route('show', $flat -> id)}}">
            <div class="col-xs-12 col-md-4 blocco-flat" data-id="{{$flat -> id}}" style="height: 100%;">
                <div id="carouselExampleControls{{$flat -> id}}" class="carousel slide" data-interval="false" style="border-radius:10px; margin-bottom: 10px;">
                    <div class="carousel-inner " style="border-radius: 10px;box-shadow: 0 0.3rem 1rem rgba(0, 0, 0, 0.35) !important;">
                        <div class="carousel-item active">
                            <img src=" {{asset($flat -> photo_url)}}" alt="">
                        </div>
                        <div class="carousel-item">
                            <img src=" {{asset($flat -> photo_url)}}" alt="">
                        </div>
                        <div class="carousel-item">
                            <img src=" {{asset($flat -> photo_url)}}" alt="">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls{{$flat -> id}}" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls{{$flat -> id}}" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                    <p style="margin-bottom: 1px;color: #f8fafc;position: absolute;bottom: 0px;left: 11px;font-size: 21px;"><strong>{{$flat -> price_at_night}} € </strong></p>
                </div>
        </a>

        <h4 class="titoloappsearch">{{$flat -> title}}</h4>
        <p class="descrizione">{{$flat -> description}}</p>
    </div>
    @endif
    @endforeach
</div>
</div>
@endsection
