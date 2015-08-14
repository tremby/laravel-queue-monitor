<table class="table">
    <thead>
        <th>
            Queue name
        </th>
        <td>
            Status
        </td>
        <td>
            Details
        </td>
        <td>
            Check queued
        </td>
        <td>
            Queue job completed
        </td>
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
