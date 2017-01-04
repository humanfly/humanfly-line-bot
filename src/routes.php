<?php
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\UnknownEventTypeException;
use LINE\LINEBot\Exception\UnknownMessageTypeException;
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/callback', function (\Slim\Http\Request $req, \Slim\Http\Response $res) {
    /** @var \LINE\LINEBot $bot */
    $bot = $this->bot;
    /** @var \Monolog\Logger $logger */
    $logger = $this->logger;
    $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
    if (empty($signature)) {
        return $res->withStatus(400, 'Bad Request');
    }
    // Check request with signature and parse request
    try {
        $events = $bot->parseEventRequest($req->getBody(), $signature[0]);
    } catch (InvalidSignatureException $e) {
        return $res->withStatus(400, 'Invalid signature');
    } catch (UnknownEventTypeException $e) {
        return $res->withStatus(400, 'Unknown event type has come');
    } catch (UnknownMessageTypeException $e) {
        return $res->withStatus(400, 'Unknown message type has come');
    } catch (InvalidEventRequestException $e) {
        return $res->withStatus(400, "Invalid event request");
    }
    foreach ($events as $event) {
        if (!($event instanceof MessageEvent)) {
            $logger->info('Non message event has come');
            continue;
        }
        if (!($event instanceof TextMessage)) {
            $logger->info('Non text message has come');
            continue;
        }
        $replyText = $event->getText();
        //$logger->info('Reply text: ' . $replyText);

        if(preg_match('/Who[\s]+am[\s]+I/i', $replyText)){
            $profileRes = $bot->getProfile($event->getUserId());
            if ($profileRes->isSucceeded()) {
                $profile = $profileRes->getJSONDecodedBody();
                $bot->replyText($event->getReplyToken(), $profile['displayName']);
            }
        } elseif (preg_match('/sticker/i', $replyText)){
            $stickerMessageBuilder = new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(2, rand(501, 527));
            $bot->replyMessage($event->getReplyToken(), $stickerMessageBuilder);
        } elseif (preg_match('/dance/i', $replyText)){
            $image = "https://soliloqueue.files.wordpress.com/2015/01/dancing.gif?w=614";
            $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($image, $image);
            $bot->replyMessage($event->getReplyToken(), $imageMessageBuilder);
        }else{
            $bot->replyText($event->getReplyToken(), $replyText);
        }

         //$logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
    }
    //$res->write('OK');
    //return $res;
});
