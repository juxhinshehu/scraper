<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Goutte\Client;
use getID3;

class TikTokScraperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile($userId)
    {
        $fullName = "";
        $isVerified = "";
        $bioText = "";
        $thumbnailImg = "";
        $nrFans = "";
        $nrHearts = "";
        $nrFollowing = "";

        $counter = 0;

        $client = new Client();
        $crawler = $client->request('GET', 'https://www.tiktok.com/@'.$userId);
        
        $crawler->filter('.share-title')->each(function ($node) use (&$fullName) {
            $fullName = $node->text();
        });

        $crawler->filter('.count-infos > .number')->each(function ($node) use (&$nrFollowing, &$nrFans, &$nrHearts, &$counter) {
            $counter++;
            if ($counter == 1) {
                $nrFollowing = $node->text();
            } else if ($counter == 2) {
                $nrFans = $node->text();
            } else if ($counter == 3) {
                $nrHearts = $node->text();
            }
        });

        $crawler->filter('.share-desc')->each(function ($node) use (&$bioText) {
            $bioText = $node->text();
        });

        $crawler->filter('.jsx-1552556901')->each(function ($node) use (&$isVerified) {
            $verificationText = $node->text();
            if ($verificationText == "Verified account") {
                $isVerified = true;
            } else {
                $isVerified = false;
            }
        });

        $crawler->filter('.avatar > div')->each(function ($node) use (&$thumbnailImg){
            $thumbnailImg = $node->attr('style');
        });

        $this->saveProfile($userId, $fullName, $isVerified, $bioText, $thumbnailImg, $nrFans, $nrHearts, $nrFollowing);

        return Response::json(['data' => "profile $userId scrapped succesfully"], 200);

    }

    private function saveProfile($userId, $fullName, $isVerified, $bioText, $thumbnailImg, $nrFans, $nrHearts, $nrFollowing) 
    {
        $profile = Profile::where('user_id', $userId)->first();
        if (empty($profile)) {
            $profile = new Profile;
            $profile->user_id = $userId;
        }

        $profile->full_name = $fullName;
        $profile->is_verified = $isVerified;
        $profile->bio_text = $bioText;
        $profile->thumbnail_img = $thumbnailImg;
        $profile->nr_fans = $nrFans;
        $profile->nr_hearts = $nrHearts;
        $profile->nr_following = $nrFollowing;
        $profile->save();
    }

    //https://www.tiktok.com/@realmadrid/video/6721977173101579526
    public function video($profileId, $videoId) 
    {
        $videoUrl = "";
        $videoDescription = "";
        $nrOfInteractions = "";
        $nrOfComments = "";
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.tiktok.com/@'.$profileId.'/video/'.$videoId);

        $crawler->filter('video')->each(function ($node) use (&$videoUrl) {
            $videoUrl = $node->attr('src');
        });

        $crawler->filter('.video-meta-title')->each(function ($node) {
            $videoDescription = $node->text();
        });

        $crawler->filter('.video-meta-count')->each(function ($node) {
            $interactions = $node->text();
            $interactions = explode("·", $interactions);
            $nrOfInteractions = trim(str_replace("likes", "", $interactions[0]));
            $nrOfComments = trim(str_replace("comments", "", $interactions[1]));
        });

        $filename = storage_path()."/".$profileId."_".$videoId;
        if (file_put_contents($filename, file_get_contents($videoUrl))) {        
              $getID3 = new getID3;
              $file = $getID3->analyze($filename);
              // unlink($filename);

              $this->saveVideo($videoUrl, $filename, $file['playtime_string'], $videoDescription, $nrOfInteractions, $nrOfComments);

            return Response::json(['data' => "video $videoId scraped succesfully"], 200);
        }

    }

    private function saveVideo($url, $internalUrl, $duration, $description, $nrOfInteractions, $nrOfComments) 
    {
        $video = Video::where('url', $url)->first();
        if (empty($video)) {
            $video = new Video;
            $video->url = $url;
        }

        $video->duration = $duration ;
        $video->internal_url = $internalUrl;
        $video->description = $description;
        $video->nr_of_interactions = $nrOfInteractions;
        $video->nr_of_comments = $nrOfComments;

        $video->save();
    }

}
