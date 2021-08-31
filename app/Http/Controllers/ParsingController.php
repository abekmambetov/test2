<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use App\Models\RequestLog;
use Illuminate\Support\Facades\DB;

class ParsingController extends Controller
{
    
    public function parseNews()
    {

        $url="http://static.feed.rbc.ru/rbcss/logical/footer/news.rss";

        $curlResult = geturl($url);
       
        // Logging request
        $RequestLog = new RequestLog();
        $RequestLog->request_method = $curlResult['request_method'];
        $RequestLog->request_url = $url;
        $RequestLog->response_http_code = $curlResult['httpCode'];
        $RequestLog->response_body = $curlResult['response'];
        $RequestLog->save();

        if($curlResult['httpCode']!=200) exit();

        // Parse news
        $data = $curlResult['response'];

        $xml   = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

        $array = json_decode(json_encode((array)$xml), TRUE);
        
        $news = $array["channel"]["item"];

        foreach ($news as $news_item) {

            $result = DB::table('news_items')
             ->select('id')
             ->where('description', '=', $news_item['description'])
             ->get();

            if(count($result) > 0) {
                continue;
            }

            $NewsItem = new NewsItem();
            $NewsItem->title = $news_item['title'];
            $NewsItem->url = $news_item['link'];
            $NewsItem->description = $news_item['description'];
   
            $NewsItem->publish_date = strtotime($news_item['pubDate']);

            if(isset($news_item['author'])) {
                $NewsItem->author = $news_item['author'];
            }
          
            $images = [];

            if(isset($news_item['enclosure'])) {

                if(count($news_item['enclosure']) > 1) {

                    foreach ($news_item['enclosure'] as $enclosure) {
                        if($enclosure["@attributes"]["type"]=="image/jpeg") {
                            $images[] = $enclosure["@attributes"]["url"];
                        }
                    }

                } else if(count($news_item['enclosure']) == 1 && $news_item['enclosure']["@attributes"]["type"]=="image/jpeg") {
                    $images[] = $news_item['enclosure']["@attributes"]["url"];
                }
            }

            if(count($images) > 0) {
                $NewsItem->img = serialize($images);
            }
            
            $NewsItem->save();
        }


    }
    
}
