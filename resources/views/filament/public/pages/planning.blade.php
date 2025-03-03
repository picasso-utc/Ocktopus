<?php
function shufl($array) {
    $keys = array_keys($array);
    shuffle($keys);
    $shuffled = [];
    foreach ($keys as $key) {
        $shuffled[$key] = $array[$key];
    }
    return $shuffled;
}
?>

<x-filament::page>
    <div style="padding: 20px; font-family: Arial, sans-serif;">
        <form wire:submit.prevent="generateSchedule" style="margin-top: 20px;">
            {{ $this->form }}
            <button type="submit" style="background-color: #007bff; color: white; font-weight: bold; padding: 10px 15px; margin: 15px; border: none; border-radius: 5px; cursor: pointer;">Générer le Planning</button>
            @if (!empty($generatedSchedule))
            <button wire:click="generateExcel" style="background-color: #28a745; color: white; font-weight: bold; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 15px;">
                Télécharger en Excel
            </button>
            @endif
        </form>

        @if (!empty($generatedSchedule))
            <h3 style="font-size: 20px; font-weight: bold; margin-top: 30px;">Planning Généré</h3>
            <div style="overflow-x: auto; background-color: white; padding: 20px; box-shadow: 0px 4px 6px rgba(0,0,0,0.1); border-radius: 10px;">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc; text-align: center;">
                    <thead>
                        <tr style="background-color: #f8f8f8;">
                            <th style="border: 1px solid #ccc; padding: 10px; color: #000">Participant</th>
                            @foreach (array_keys($generatedSchedule) as $time)
                                <th style="border: 1px solid #ccc; padding: 10px; color: #000">{{ $time }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $colors = [
                                'Bar' => '#FFA500',
                                'Caisse' => '#007BFF',
                                'Sécu Pente' => '#FF0000', 
                                'Sécu Escalier' => '#FF1D8D',
                                'Ménage' => '#32CD32',
                                'Sécu Trottoir' => '#800080', 
                            ];
                            $participants = [];
                            foreach ($generatedSchedule as $time => $tasks) {
                                foreach ($tasks as $participant => $task) {
                                    $participants[$participant][$time] = $task;
                                }
                            }
                            $participants = shufl($participants);
                        @endphp

                        @foreach ($participants as $participant => $tasks)
                            <tr>
                                <td style="border: 1px solid #ccc; padding: 10px; font-weight: bold; color: #000">{{ $participant }}</td>
                                @foreach (array_keys($generatedSchedule) as $time)
                                    @php
                                        $task = $tasks[$time] ?? '';
                                        $bgColor = $task ? ($colors[$task] ?? '#B1B1B1') : '#FFFFFF'; // Gris si inconnu, blanc si vide
                                        $textColor = ($task && isset($colors[$task])) ? 'white' : 'black';
                                    @endphp
                                    <td style="border: 1px solid #ccc; padding: 10px; background-color: {{ $bgColor }}; color: {{ $textColor }};">
                                        {{ $task }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament::page>