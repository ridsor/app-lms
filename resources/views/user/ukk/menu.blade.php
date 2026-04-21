<div class="card rounded-responsive">
  <div class="card-body">
    <ul class="d-flex gap-2 row-gap-3 flex-wrap">
      <li>
        <a href="{{ !Request::routeIs('user.ukk.show') ? route('user.ukk.show', ['id' => $ukk->id]) : '' }}"
          class="py-2 px-2 {{ Request::routeIs('user.ukk.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Info
          UKK</a>
      </li>
      @if ($ukk->type === 'Teori')
        <li>
          <a href="{{ !Request::routeIs('user.ukk.question.show') ? route('user.ukk.question.show', ['id' => $ukk->id]) : '' }}"
            class="py-2 px-2 {{ Request::routeIs('user.ukk.question.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Soal</a>
        </li>
        @can('ukk.evaluation')
          <li>
            <a href="{{ !Request::routeIs('user.ukk.result.teori') ? route('user.ukk.result.teori', ['id' => $ukk->id]) : '' }}"
              class="py-2 px-2 {{ Request::routeIs('user.ukk.result.teori') ? 'border-bottom border-primary' : 'text-secondary' }}">Hasil</a>
          </li>
        @endcan
      @elseif ($ukk->type === 'Praktik')
        <li>
          <a href="{{ !Request::routeIs('user.ukk.result.praktik') ? route('user.ukk.result.praktik', ['id' => $ukk->id]) : '' }}"
            class="py-2 px-2 {{ Request::routeIs('user.ukk.result.praktik') ? 'border-bottom border-primary' : 'text-secondary' }}">Hasil</a>
        </li>
      @endif
    </ul>
  </div>
</div>
