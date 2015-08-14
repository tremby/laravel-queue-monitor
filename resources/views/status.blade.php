<table class="table">
    <thead>
        <th>
            Queue name
        </th>
        <th>
            Status
        </th>
        <th>
            Details
        </th>
        <th>
            Check queued
        </th>
        <th>
            Queue job completed
        </th>
    </thead>
    <tbody>
        @foreach ($queues as $queueStatus)
            <tr class="
                @if ($queueStatus->isError())
                    danger
                @elseif ($queueStatus->isOk())
                    success
                @elseif ($queueStatus->isPending())
                    warning
                @endif
            ">
                <td>
                    <code>
                        {{ $queueStatus->getQueueName() }}
                    </code>
                </td>
                <td>
                    {{ $queueStatus->getStatus() }}
                </td>
                <td>
                    {{ $queueStatus->getMessage() }}
                </td>
                <td>
                    @include('queue-monitor::date', ['date' => $queueStatus->getStartTime()])
                </td>
                <td>
                    @include('queue-monitor::date', ['date' => $queueStatus->getEndTime()])
                    @if (($start = $queueStatus->getStartTime()) && ($end = $queueStatus->getEndTime()))
                        <span class="text-muted">
                            ({{ $end->diffForHumans($start) }})
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
