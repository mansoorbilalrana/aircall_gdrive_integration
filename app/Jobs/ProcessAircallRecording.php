<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \Spatie\WebhookClient\ProcessWebhookJob as SpatieProcessWebhookJob;
use Illuminate\Support\Facades\Log;
use \Spatie\WebhookClient\Models\WebhookCall;
use Storage;

class ProcessAircallRecording extends SpatieProcessWebhookJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->webhookCall;
        //Do something with the eventL
        if($data->payload['data']['recording'] != null){
            $fileData = file_get_contents($data->payload['data']['recording']);
            $this->create_directory($fileData);
        }
        http_response_code(200); //Acknowledge you received the response
    }

    public function create_directory($fileData){
        $dir = '/';
        $recursive = false; // Get subdirectories also?
        $contents = collect(Storage::cloud()->listContents($dir, $recursive));

        $directory = $contents
            ->where('type', '=', 'dir')
            ->where('filename', '=', now()->month.'-'.now()->year)
            ->first();

        if($directory == null){
            Storage::cloud()->makeDirectory($directory['path'].'/'.now()->month.'-'.now()->year);
            $contents = collect(Storage::cloud()->listContents($dir, $recursive));
            $directory = $contents
            ->where('type', '=', 'dir')
            ->where('filename', '=', now()->month.'-'.now()->year)
            ->first();
        }
        $rand = now()->day.'-'.now()->month.'-'.now()->year.' | '.$data->payload['data']['raw_digits'];
        Storage::cloud()->put($directory['path'].'/'.$rand.'.mp3', $fileData);
    }


}
