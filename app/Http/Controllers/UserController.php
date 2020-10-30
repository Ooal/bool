<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Flat;
use App\Sponsor;
use App\Service;
use App\Photo;
use App\User;
use App\Visit;
use App\Message;
use Carbon\Carbon;



class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
// Home->Login/id(user)->BecomeHost->Form(Host)->

// Mostra il form per l'upgrade User 2 Host
    public function becomeHost(){
        $flats = Flat::all();
        $services = Service::all();
        return view('become_host', compact('flats', 'services'));
    }

    public function update($id){
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $services = Service::all();
        return view('edit_flat', compact('flat', 'services','flats'));
    }

    public function editFlat(Request $request, $id){   //$id => $id(flat)
      /*$flat = Flat::findOrFail($id);*/

        $services = Service::all();
        $data = $request -> all();

        $data = $request -> validate([
            'title' => ['required', 'string', 'min:5', 'max:80'],
            'description' => ['required', 'string', 'min:5', 'max:1000'],
            'type' => 'required',
            'photo_url' => 'required|image|mimes:JPG,jpeg,png,jpg,webp',
            'price_at_night' => 'required|integer|gte:1',
            'mq' => 'required|integer|gte:5',
            'number_of_bed' => 'required',
            'number_of_bathroom' => 'required',
            'number_of_room' => 'required',
            'WiFi' => 'integer',
            'Parking_Spot' => 'integer',
            'Pool' => 'integer',
            'Reception' => 'integer',
            'Sauna' => 'integer',
            'Sea_View' => 'integer',
            'latitude' => 'string',
            'longitude' => 'string',
            'address' => ['required', 'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9 \-_\,ìùàòèé]+)$/']
            // /^(?:[A-Za-z]+)(?:[A-Za-z0-9 _]*)$/
            // regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/
            ]);




        /*$address = explode("," , $data['address']);
        $data['street']=$address[0];
        $data['city']=$address[1];
        $data['region']=$address[2];
        $data['state']=$address[3];*/


        // prendo tutta l'array dalla request
        $imagePath = $request-> photo_url;
        // prendo solo in nome originale
        $imageName = $imagePath->getClientOriginalName();
        // creo una variabile con dentro le info per per il savataggio e faccio il prepend della data attuale in secondi per evitare conflitti nel nome
        $filePath = $request-> photo_url ->storeAs('images', time().$imageName, 'public');
        // aggiungo la stringa del percorso /storage/ da aggiungere al DB
        $data['photo_url'] = '/storage/'.$filePath;



        // $complete_address_array = [];
        // $only_street_numb =[];
        //
        // array_push($only_street_numb, $data['street_type'], $data['street'], $data['street_number']);
        // $only_street_numb = implode(" ", $only_street_numb);
        //
        // array_push($complete_address_array, $only_street_numb, $data['city'], $data['region'], $data['state']);
        // $complete_address = implode(", ", $complete_address_array);


        $userid = Auth::user()-> id;
        // prendo da data l'user_id della tabella e gli assegno l'id dell'Utente attuale
        $data['user_id'] = $userid;
        // $data['address'] = $complete_address;
        // $user = User::findOrFail($id);


        $flat = Flat::where('id', $id)->update($data);
        /*if (isset($data['WiFi'])) {
          $wifi_id = $data['WiFi'];
          $service = Service::findOrFail($wifi_id);
          $f = $flat-> services-> first()-> pivot-> id;
          $service-> flats()->wherePivot('id',$f)->updateExistingPivot($wifi_id, ['service_id' => $wifi_id]);

        }*/
        /*if (isset($data['Parking_Spot'])) {
          $park_id = $data['Parking_Spot'];
          $service = Service::findOrFail($park_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Pool'])) {
          $pool_id = $data['Pool'];
          $service = Service::findOrFail($pool_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Reception'])) {
          $rece_id = $data['Reception'];
          $service = Service::findOrFail($rece_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Sauna'])) {
          $sauna_id = $data['Sauna'];
          $service = Service::findOrFail($sauna_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Sea_View'])) {
          $sea_id = $data['Sea_View'];
          $service = Service::findOrFail($sea_id);
          $service -> flats() -> attach($flat);
        }*/
    return redirect() -> route('index') -> with('status', 'Appartamento Modificato!!!');
    }

    // nel form al momento de click su submit per far diventare un User->Host
    public function storehost(Request $request, $id ){   //$id => $id(user)

        $services = Service::all();
        $data = $request -> all();

        $data = $request -> validate([
            'title' => ['required', 'string', 'min:5', 'max:80'],
            'description' => ['required', 'string', 'min:5', 'max:1000'],
            'type' => 'required',
            'photo_url' => 'required|image|mimes:JPG,jpeg,png,jpg,webp',
            'price_at_night' => 'required|integer|gte:1',
            'mq' => 'required|integer|gte:5',
            'number_of_bed' => 'required',
            'number_of_bathroom' => 'required',
            'number_of_room' => 'required',
            'WiFi' => 'integer',
            'Parking_Spot' => 'integer',
            'Pool' => 'integer',
            'Reception' => 'integer',
            'Sauna' => 'integer',
            'Sea_View' => 'integer',
            'latitude' => 'string',
            'longitude' => 'string',
            'address' => ['required', 'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9 \-_\,ìùàòèé]+)$/']
            // /^(?:[A-Za-z]+)(?:[A-Za-z0-9 _]*)$/
            // regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/
            ]);



        /*$address = explode("," , $data['address']);
        $data['street']=$address[0];
        $data['city']=$address[1];
        $data['region']=$address[2];
        $data['state']=$address[3];*/


        // prendo tutta l'array dalla request
        $imagePath = $request-> photo_url;
        // prendo solo in nome originale
        $imageName = $imagePath->getClientOriginalName();
        // creo una variabile con dentro le info per per il savataggio e faccio il prepend della data attuale in secondi per evitare conflitti nel nome
        $filePath = $request-> photo_url ->storeAs('images', time().$imageName, 'public');
        // aggiungo la stringa del percorso /storage/ da aggiungere al DB
        $data['photo_url'] = '/storage/'.$filePath;







        // prendo da data l'user_id della tabella e gli assegno l'id dell'Utente attuale
        $data['user_id'] = $id;
        // $user = User::findOrFail($id);

        $flat = Flat::create($data);


        if (isset($data['WiFi'])) {
          $wifi_id = $data['WiFi'];
          $service = Service::findOrFail($wifi_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Parking_Spot'])) {
          $park_id = $data['Parking_Spot'];
          $service = Service::findOrFail($park_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Pool'])) {
          $pool_id = $data['Pool'];
          $service = Service::findOrFail($pool_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Reception'])) {
          $rece_id = $data['Reception'];
          $service = Service::findOrFail($rece_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Sauna'])) {
          $sauna_id = $data['Sauna'];
          $service = Service::findOrFail($sauna_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Sea_View'])) {
          $sea_id = $data['Sea_View'];
          $service = Service::findOrFail($sea_id);
          $service -> flats() -> attach($flat);
        }

        return redirect() -> route('index') -> with('status', 'Nuovo Appartamento Creato!!!');
    }

    public function storeMessagesUser(Request $request, $id){
        $usermail = Auth::user()-> email;
        $data = $data = $request -> all();
        $data = $request -> validate([
            'name' => ['required', 'min:2', 'max:40'],
            'subject' => ['required', 'min:2', 'max:40'],
            'message' => ['required', 'min:2', 'max:255']
            ]);
        $data['flat_id'] = $id;
        $data['email'] = $usermail;
        $message = Message::create($data);

        return redirect() -> route('show',$id)-> with('status', 'Messaggio Inviato');
    }

    public function showProfile(){
        $flats = Flat::all();
        $sponsors = Sponsor::all();
        $date = Carbon::now();

        return view('profile', compact('flats','sponsors', 'date'));
    }

    public function sponsorForm($id){
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $sponsors = Sponsor::all();
        return view('sponsor_form', compact('sponsors', 'flat','flats'));
    }

    public function sponsorPayment(Request $request, $id){ //id del Flat
        $data = $request -> all();
        $flat = Flat::findOrFail($id);
        $data = $request -> validate([
            'sponsor' => ['required']
            ]);
        $sponsor_array = explode('/', $data['sponsor']);
        $sponsorId = $sponsor_array[0];
        $sponsorDur = $sponsor_array[1];
        $sponsor = Sponsor::findOrFail($sponsorId);
        $flat = Flat::findOrFail($id);


        $date = Carbon::now();
        $carbon_date = Carbon::parse($date);
        $carbon_date->addHours($sponsorDur);

        $var1= Carbon::parse($carbon_date);

        // if ($var1 > $date){
        //   dd('OK');
        // }


        // prende la riga del DB di "Sponsor" e la associa alla riga del DB di Flat
        $sponsor-> flats() -> attach($flat, ['date_end'=> $carbon_date]);

        return redirect() -> route('profile') -> with('status', 'Pagamento approvato!!!');
    }

    public function sponsorFormUpdate($id){
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $sponsors = Sponsor::all();
        return view('sponsor_form_update', compact('sponsors', 'flat','flats'));
    }

    public function sponsorPaymentUpdate(Request $request, $id){ //id del Flat
        $data = $request -> all();
        $data = $request -> validate([
            'sponsor' => ['required']
            ]);
        $sponsor_array = explode('/', $data['sponsor']);
        $sponsorId = $sponsor_array[0];
        $sponsorDur = $sponsor_array[1];
        $sponsor = Sponsor::findOrFail($sponsorId);
        $flat = Flat::findOrFail($id);


        $date = Carbon::now();
        $carbon_date = Carbon::parse($date);
        $carbon_date->addHours($sponsorDur);

        $var1= Carbon::parse($carbon_date);

        // if ($var1 > $date){
        //   dd('OK');
        // }

        $flat_sponsorID = $flat-> sponsors-> first()-> pivot-> id;
        $spoID = $flat-> sponsors-> first()-> pivot-> sponsor_id;

        // prende la riga del DB di "Sponsor" e la associa alla riga del DB di Flat
        $flat-> sponsors()->wherePivot('id',$flat_sponsorID)->updateExistingPivot($spoID, ['sponsor_id' => $sponsorId,'date_end' => $carbon_date]);



        return redirect() -> route('profile') -> with('status', 'Pagamento approvato!!!');
    }

    public function disable($id){
        $flat = Flat::findOrFail($id);
        $flat -> update(array('disactive' => 1));
        return redirect() -> route('profile');
    }
    public function enable($id){
        $flat = Flat::findOrFail($id);
        $flat -> update(array('disactive' => 0));
        return redirect() -> route('profile');
    }
    public function delete($id){
        $flat = Flat::findOrFail($id);
        $flat -> update(array('deleted' => 1));
        return redirect() -> route('profile');
    }
    public function showMessage($id){
        $flat = Flat::findOrFail($id);
        $flats = Flat::all();
        $messages = Message::all();
        return view('message', compact('flat', 'messages','flats'));
    }

    public function showStats($id){

      $flats = Flat::all();
      $flat = Flat::findOrFail($id);
      $visits = Visit::all();
      $messages = Message::all();
      $visitTOT = 0;
      $visitTOTtoday = 0;
      $messageTOT = 0;
      $massageTOTtoday = 0;
      $dataVisits = [
        0=>0,
        1=>0,
        2=>0,
        3=>0,
        4=>0,
        5=>0,
        6=>0,
        7=>0,
        8=>0,
        9=>0,
        10=>0,
        11=>0,
        12=>0,
        13=>0,
        14=>0,
        15=>0,
        16=>0,
        17=>0,
        18=>0,
        19=>0,
        20=>0,
        21=>0,
        22=>0,
        23=>0
      ];
      $dataMessages= [
        0=>0,
        1=>0,
        2=>0,
        3=>0,
        4=>0,
        5=>0,
        6=>0,
        7=>0,
        8=>0,
        9=>0,
        10=>0,
        11=>0,
        12=>0,
        13=>0,
        14=>0,
        15=>0,
        16=>0,
        17=>0,
        18=>0,
        19=>0,
        20=>0,
        21=>0,
        22=>0,
        23=>0
      ];

      foreach ($visits as $visit) {
        $from_date2hour = Carbon::parse($visit['date'])-> format('Ymd');
        $data_hour_now = Carbon::now()-> format('Ymd');
        $counter = $visit['counter'];

        if ($visit['flat_id'] == $id) {
          $visitTOT += $counter;
          if ( $data_hour_now == $from_date2hour) {
            $visitTOTtoday += $counter;
            $hour = intval(Carbon::parse($visit['date'])-> format('H'));
            $dataVisits[$hour] = $counter;
          }
        }
      }

      foreach ($messages as $message) {
        $from_day = Carbon::parse($message['created_at'])-> format('Ymd');
        $data_day_now = Carbon::now()-> format('Ymd');
        if ($message['flat_id'] == $id) {
            $messageTOT += 1;
            if ( $data_day_now == $from_day) {
              $massageTOTtoday += 1;
              $from_date2hour = Carbon::parse($message['created_at'])-> format('H');
              $data_hour_now = Carbon::now()-> format('H');
              if ($from_date2hour == $data_hour_now) {
                $hour = intval(Carbon::parse($message['created_at'])-> format('H'));
                $dataMessages[$hour] +=1;
              }else {
                $hour = intval(Carbon::parse($message['created_at'])-> format('H'));
                $dataMessages[$hour] +=1;
              }
            }
        }
      }

      $datamessage = implode(' ' , $dataMessages);
      $datavisit = implode(' ' , $dataVisits);
      return view('stats', compact('visitTOT','visitTOTtoday', 'messageTOT','massageTOTtoday','datavisit','datamessage','flats'));
}
}
