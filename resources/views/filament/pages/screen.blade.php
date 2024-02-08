<x-filament-panels::page>

    <section id='gallery'></section>

    <section id='controls'>
        <input type='text' id='identity' placeholder='Enter your name' value=" {{ fake()->unique()->name() }}" required>
        <button id='button-join'>Join Room</button>
        <button id='button-leave' disabled>Leave Room</button>
        <button id='button-raise-hand' disabled>Raise Hand ✋</button>
        <button id='button-mute-all' disabled>Mute All</button>


    </section>

    <section id='status'>
        <div id='status-message'></div>
    </section>



    fasfasfasd {{$user}}

{{$mogo}}



    <style>

        body {
            margin: 0;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: sans-serif;
        }

        #gallery {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            align-content: flex-start;
            padding: 15px;
            gap: 5px;
        }

        .participant {
            background-color: #36363a;
            border-radius: 5px;
            padding: 8px;
            position: relative;
        }

        .participant video {
            max-width: 22rem;
            margin: 0 auto;
        }

        #controls {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            gap: 5px;
        }

        input {
            background: rgb(233, 230, 230);
            border: solid 1px rgb(211, 209, 209);
            text-align: center;
            height: 21px;
            font-size: 15px;
            vertical-align: bottom;
            padding: 8px;
        }

        button {
            background: rgb(41, 103, 184);
            color: rgb(255, 255, 255);
            border: solid 1px #CCC;
            color: white;
            padding: 12px;
        }

        .identity {
            position: absolute;
            left: 0;
            bottom: 0.9375rem;
            color: rgb(255, 255, 255);
            padding-left: 0.9375rem;
            z-index: 10;
        }

        input:disabled {
            color: #999;
        }

        button:hover, button:active, button:disabled {
            background: rgb(124, 169, 227);
        }

        @media only screen and (max-width: 500px) {
            #controls {
                flex-direction: column;
                align-items: stretch;
            }
        }

        #status {
            align-items: center;
        }

        #status-message {
            text-align: center;
            color: rgb(225, 73, 73);
        }
        button.raised {
            background-color: rgb(145, 237, 145);
        }

        div.raised {
            position: relative;
        }

        div.raised::before {
            position: absolute;
            top: 25px;
            left: 25px;
            z-index: 2;
            content: "✋";
            transform: scale(2);
        }

    </style>

    <div
        x-data="{}"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('twilio'))]"
    >
        <!-- ... -->
    </div>
    <div
        x-data="{}"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('video-hand-raise'))]"
    >
        <!-- ... -->
    </div>
    <script>
        var csrfToken = '{{ csrf_token() }}';
    </script>

</x-filament-panels::page>
