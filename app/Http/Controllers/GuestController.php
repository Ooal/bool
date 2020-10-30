<?php
namespace App\Http\Controllers;
use App\Flat;
use App\Sponsor;
use App\Service;
use App\Photo;
use App\User;
use App\Visit;
use App\Message;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(){
        $flats = Flat::all();
        $sponsors = Sponsor::all();
        return view('index', compact('flats','sponsors'));
    }


    public function search(){

      $sponsors = Sponsor::all();
      $services = Service::all();
      $flats = Flat::all();
      $latitude = $_COOKIE['lat'];
      $longitude = $_COOKIE['long'];
      $city = '';
      if (empty($_COOKIE['distance'])) {
        $distance = 20;
      }
      else {
        $distance = $_COOKIE['distance'];
      }

      if (!(empty($latitude))) {
          $city = $_COOKIE['city'];
        $flatselect = Flat::select(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance'))
      ->having('distance', '<', $distance)
      ->orderBy('distance')
      ->get();
      }
      else {
        $flatselect = Flat::all();
      }

        return view('search', compact('flats','sponsors', 'services','city','flatselect'));
    }

    public function searchsort(request $request){
      if (empty($_COOKIE['nofroom'])) {
        $nofroom = 0;
      }
      else {
        $nofroom = $_COOKIE['nofroom'];
      }
      if (empty($_COOKIE['nofbed'])) {
        $nofbed = 0;
      }
      else {
        $nofbed = $_COOKIE['nofbed'];
      }
        $data = $request -> all();
        $srvs = $data['service'];
        if(isset($srvs)){
            $arraySrvs = explode(',', $srvs);
        } else {
            $arraySrvs = [];
        }
        $flats = Flat::where([['disactive', '=', '0'],['deleted', '=', '0'],['number_of_bed', '>=', $nofbed],['number_of_room', '>=', $nofroom]]) -> get();
        foreach ($flats as $flat) {
            $services = Flat::findOrFail($flat['id']) -> services() -> get();
            $aptSrvs = [];
            foreach ($services as $service) {
                $aptSrvs[] = $service['id'];
            }
            $containsAllValues = !array_diff($arraySrvs, $aptSrvs);

            $flat['services'] = $containsAllValues;
        }
        $response = $flats -> where('services', '=', true);
        return response() -> json($response);

    }

    public function show($id){
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $visits = Visit::all();
        $photo = Photo::all();
        $services = Service::all();
        $messages = Message::all();
        $date = Carbon::now();

        if (empty(Auth::user()-> id) || (Auth::user()-> id != $flat['user_id']) ){
          if (!(empty($visits->first() ))) {
            $data_hour_now = Carbon::now()-> format('YmdH');
            $conteggio_aumentato=false;
            foreach ($visits as $visit) { // comincio a ciclare ma non scrivo mai niente
              $from_date2hour = Carbon::parse($visit['date'])-> format('YmdH');
              if ($visit['flat_id'] == $flat['id'] && $data_hour_now == $from_date2hour){  // se trovo un match allora modifico la variabile d'appoggio e scrivo sul DB
                $visit2up = $visit['counter'] += 1;
                $visit -> update(array('counter' => $visit2up));
                $conteggio_aumentato=true; // la variabile d'appoggio cambio
                break;
              }
            }
            if($conteggio_aumentato == false){ // se tutto il ciclo è falso e la variabile non è cambiata allora creo un nuova riga
              $data = ['date' => $date,
              'flat_id' => $id,
              'counter' => 1];
              $row_visit = Visit::create($data);
            }
          }
          else { // se nella tabella non esiste nulla allora scrivo una nuova riga
            $data = ['date' => $date,
            'flat_id' => $id,
            'counter' => 1];
            $row_visit = Visit::create($data);
          }
        }
        return view('show', compact('flat','flats','photo','services'));
    }

    public function storeMessagesGuest(Request $request, $id){
        $data = $request -> all();
        $data = $request -> validate([
            'name' => ['required', 'min:2', 'max:40'],
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/^(?=.*\.)/'],
            'subject' => ['required', 'min:2', 'max:40'],
            'message' => ['required', 'min:2', 'max:255']
            ]);
        $data['flat_id'] = $id;
        $message = Message::create($data);
        return redirect() -> route('index')-> with('status', 'Messaggio Inviato');
    }

}
