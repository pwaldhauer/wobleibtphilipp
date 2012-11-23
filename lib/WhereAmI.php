<?php

require_once('lib/php-foursquare/src/FoursquareAPI.class.php');

class Status {

    const FAIL = 'fail';
    const ASLEEP = 'asleep';
    const ON_THE_WAY = 'on_the_way';
    const WORKING = 'working';

    public $status;
    public $last_checkin;
    public $eta;

}

class Checkin {
	
    public $venue_id;
    public $venue;
    public $date;

}

class WhereAmI {

    private $foursquare;
    private $cache_time = 300;
    private $cache_file = 'checkin_cache';

    private $last_threshold = 30000;

    private $start_venue = '';
    private $end_venue = '';

    private $way = array();

    public function initializeFoursquare($consumer_id, $consumer_secret, $token) {
        $this->foursquare = new FoursquareAPI($consumer_id, $consumer_secret);
        $this->foursquare->SetAccessToken($token);
    }

    public function setStartStop($start_venue, $end_venue) {
        $this->start_venue = $start_venue;
        $this->end_venue = $end_venue;
    }

    public function addWayPart($name, $duration, $times = null) {
        $this->way[] = array(
            'name' => $name,
            'duration' => $duration,
            'times' => $times,
        );
    }

    public function getCurrentStatus() {
        $last_checkin = $this->_getLastCheckin();

        $result = new Status();
        $result->status = Status::FAIL;
        $result->last_checkin = $last_checkin;
        $result->eta = null;

        if($last_checkin->date + $this->last_threshold < time()) {
            $result->status = Status::ASLEEP;
            return $result;
        }

        if($last_checkin->venue_id == $this->end_venue) {
            $result->status = Status::WORKING;
            return $result;
        }

        if($last_checkin->venue_id == $this->start_venue) {
            $result->status = Status::ON_THE_WAY;
            $result->eta = $this->_getEta($last_checkin->date);

            if($result->eta == null) {
                $result->status = Status::FAIL;
            }

            return $result;
        }
     
        return $result;
    }

    private function _getEta($checkin_date) {
        $total = $checkin_date;
        foreach($this->way as $way_part) {
            $hours = $this->_numberToSeconds(date('Hi', $total));

            if($way_part['times'] == null) {
                $total += $way_part['duration'];
                continue;
            }

            $dif = -1;
            foreach($this->_prepareTimes($way_part['times']) as $time) {
                if($time < $hours) {
                    continue;
                }

                $dif = $time - $hours;
                break;
            }

            if($dif == -1) {
                return null;
            }

            $total += $dif + $way_part['duration'];
        }

        return $total;
    }

    private function _getLastCheckin() {
        $foursquare_data = $this->_loadCheckinData();

        $checkins = array();
        foreach ($foursquare_data->response->checkins->items as $checkin) {
            $obj = new Checkin();
            $obj->venue_id = $checkin->venue->id;
            $obj->venue = $checkin->venue->name;
            $obj->date = $checkin->createdAt;

            $checkins[] = $obj;
        }

        return $checkins[0];
    }

    private function _prepareTimes($numbers) {
        $seconds = array();

        foreach($numbers as $number) {
            $seconds[] = $this->_numberToSeconds($number);
        }

        return $seconds;
    }

    private function _numberToSeconds($number) {
        return intval($number/100) * 3600 + (($number - (intval($number/100) * 100)) * 60);
    }


    private function _loadCheckinData() {
        if(file_exists($this->cache_file) && filemtime($this->cache_file) > (time() - $this->cache_time)) {
            return json_decode(file_get_contents($this->cache_file));
        } 

        $checkins = json_decode($this->foursquare->GetPrivate('users/self/checkins', array('limit' => '20')));
        file_put_contents($this->cache_file, json_encode($checkins));

        return $checkins;
    }
}

