<h1>Detalhe do registo</h1>

<p><strong>Data:</strong> {{ $timeEntry->date->format('Y-m-d') }}</p>
<p><strong>Início:</strong> {{ $timeEntry->start_time }}</p>
<p><strong>Fim:</strong> {{ $timeEntry->end_time }}</p>
<p><strong>Duração:</strong> {{ $timeEntry->duration_minutes }} min</p>
<p><strong>Local:</strong> {{ $timeEntry->location }}</p>
<p><strong>Tipo:</strong> {{ $timeEntry->activityType->name }}</p>
<p><strong>Cliente:</strong> {{ $timeEntry->client->name }}</p>
<p><strong>Descrição:</strong> {{ $timeEntry->description }}</p>
<p><strong>Estado:</strong> {{ $timeEntry->status }}</p>

<a href="{{ route('time-entries.index') }}">Voltar</a>
<a href="{{ route('time-entries.edit', $timeEntry->id) }}">Editar</a>
