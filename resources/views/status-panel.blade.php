<div class="panel {{ @$panel_class ?: 'panel-default' }}">
    <div class="panel-heading">
        <h3 class="panel-title">
            Queue monitor
        </h3>
    </div>
    @include('queue-monitor::status')
</div>
