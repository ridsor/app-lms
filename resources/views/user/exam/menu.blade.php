<div class="card rounded-responsive">
    <div class="card-body">
        <ul class="d-flex gap-2 row-gap-3 flex-wrap">
            <li>
                <a href="{{ !Request::routeIs('user.exam.show') ? route('user.exam.show', ['id' => $exam->id]) : '' }}"
                    class="py-2 px-2 {{ Request::routeIs('user.exam.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Info
                    Ujian</a>
            </li>
            <li>
                <a href="{{ !Request::routeIs('user.exam.question.show') ? route('user.exam.question.show', ['id' => $exam->id]) : '' }}"
                    class="py-2 px-2 {{ Request::routeIs('user.exam.question.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Soal</a>
            </li>
            <li>
                <a href="{{ !Request::routeIs('user.exam.result.show') ? route('user.exam.result.show', ['id' => $exam->id]) : '' }}"
                    class="py-2 px-2 {{ Request::routeIs('user.exam.result.show') ? 'border-bottom border-primary' : 'text-secondary' }}">Hasil</a>
            </li>
        </ul>
    </div>
</div>
