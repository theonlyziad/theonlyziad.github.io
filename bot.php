<?php
header('Content-Type: application/json');

function validateBotToken($token) {
    $url = 'https://api.telegram.org/bot' . $token . '/getMe';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return isset($result['ok']) && $result['ok'] === true;
}

$response = array();

$bottoken = isset($_GET['bottoken']) ? $_GET['bottoken'] : null;

if (!empty($bottoken)) {
    if (validateBotToken($bottoken)) {
        $botFileName = 'bot_' . $bottoken . '.php';
        $botFileContent = '<?php
$bottoken = \'' . $bottoken . '\'; // Add your bot token here

function sendMessage($chatID, $message, $parse_mode = \'HTML\') {
    global $bottoken;
    $url = \'https://api.telegram.org/bot\' . $bottoken . \'/sendMessage\';
    $data = [
        \'chat_id\' => $chatID,
        \'text\' => $message,
        \'parse_mode\' => $parse_mode
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function sendChatAction($chatID, $action) {
    global $bottoken;
    $url = \'https://api.telegram.org/bot\' . $bottoken . \'/sendChatAction\';
    $data = [
        \'chat_id\' => $chatID,
        \'action\' => $action
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

$update = file_get_contents("php://input");
$update = json_decode($update, true);

if (isset($update[\'message\'])) {
    $message = $update[\'message\'];
    $chatId = $message[\'chat\'][\'id\'];
    $text = $message[\'text\'];

    if ($text === \'/start\') {
        $startMessage = "<b>👋 Hello!</b>\nI\'m here to assist you. You can start by typing \'<i>_ai_</i>\' followed by your question or message. Or use \'<b>/gen</b>\' followed by an image prompt to generate an image.";
        sendMessage($chatId, $startMessage);
    }

    if (strtolower(substr($text, 0, 4)) === \'/gen\') {
        $prompt = substr($text, 5); // Extracting the prompt after \'/gen \'

        sendChatAction($chatId, \'upload_photo\');

        $image_api_url = "https://aiimagegen.apinepdev.workers.dev/?search=" . urlencode($prompt);

        $ch = curl_init($image_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $image_response = curl_exec($ch);
        curl_close($ch);

        $image_data = json_decode($image_response, true);
        $generated_images = $image_data[\'images\'] ?? [];

        if (count($generated_images) > 0) {
            $first_image = $generated_images[0];
            file_put_contents(\'generated_image.jpg\', file_get_contents($first_image)); // Save the image locally

            $caption = "𝗚𝗲𝗻𝗲𝗿𝗮𝘁𝗲𝗱 𝗯𝘆 𝗔𝗜 𝗯𝗮𝘀𝗲𝗱 𝗼𝗻: $prompt 🖼️";

            // Sending the image file with caption
            $url = \'https://api.telegram.org/bot\' . $bottoken . \'/sendPhoto\';
            $data = [
                \'chat_id\' => $chatId,
                \'caption\' => $caption,
                \'photo\' => curl_file_create(\'generated_image.jpg\', \'image/jpeg\', \'generated_image.jpg\')
            ];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } else {
            sendMessage($chatId, \'<b>No image generated for this prompt. Please try something else!</b> 😕\');
        }
    }

    if (strtolower($text) === \'ai\') {
        $aiMessage = "<i>Please provide a query after \'<b>_ai_</b>\' to get a response. 😊</i>";
        sendMessage($chatId, $aiMessage);
    }

    if (strtolower(substr($text, 0, 3)) === \'ai \') {
        $query = substr($text, 3);

        sendChatAction($chatId, \'typing\');

        $worker_api_url = "https://chatgpt.apinepdev.workers.dev/?question=" . urlencode($query);

        $ch = curl_init($worker_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $worker_response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($worker_response, true);
        $answer = $response_data[\'answer\'] ?? \'<b>Apologies, I cannot answer your question right now. Please try another question.</b>\';

        sendMessage($chatId, "<b>😎 Here\'s the answer:</b>\n$answer");
    }
}
?>';
        file_put_contents($botFileName, $botFileContent);

        // Set up the webhook
        $webhook_url = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $botFileName;
        $url = 'https://api.telegram.org/bot' . $bottoken . '/setWebhook';
        $data = ['url' => $webhook_url];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseCurl = curl_exec($ch);
        curl_close($ch);

        if ($responseCurl !== false) {
            $result = json_decode($responseCurl, true);

            if (isset($result['ok'])) {
                if ($result['ok']) {
                    $response['status'] = "success";
                    $response['message'] = "Bot hosted successfully. Enjoy!";
                    $response['join_link'] = "https://t.me/devsnp";
                } else {
                    $response['status'] = "failure";
                    $response['message'] = "Failed to set webhook for bot with token: " . $bottoken;
                }
            } else {
                $response['status'] = "failure";
                $response['message'] = "Unexpected response format or missing 'ok' key.";
            }
        } else {
            $response['status'] = "failure";
            $response['message'] = "Failed to connect or set up the webhook for the bot.";
        }
    } else {
        $response['status'] = "failure";
        $response['message'] = "Invalid Bot Token.";
    }
} else {
    $response['status'] = "failure";
    $response['message'] = "Bot Token not provided.";
}

$activeBotFiles = glob('bot_*.php');
$response['active_bots_count'] = count($activeBotFiles);

echo json_encode($response, JSON_UNESCAPED_SLASHES);
?>