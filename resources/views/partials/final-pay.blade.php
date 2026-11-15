{{-- Final pay breakdown inside the offboarding case window (Alpine: detail.final_pay). --}}
<section class="fp" x-show="detail.final_pay">
    <div class="fp-head">
        <h4>Final pay</h4>
        <form method="POST" :action="detail.cause_url" class="fp-cause">
            @csrf
            @method('PATCH')
            <label for="fp-cause">Cause of separation</label>
            <select id="fp-cause" name="separation_cause" onchange="this.form.submit()">
                @foreach (\App\Support\FinalPay::CAUSES as $key => $label)
                    <option value="{{ $key }}" :selected="detail.final_pay.cause === '{{ $key }}'">{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <dl class="fp-facts">
        <div><dt>Date hired</dt><dd><span x-text="detail.final_pay.hired_on || 'Not set'"></span><small x-show="detail.final_pay.hired_is_estimate"> (estimated from the record date; set it in Employee Management)</small></dd></div>
        <div><dt>Length of service</dt><dd x-text="detail.final_pay.service + ' (counted as ' + detail.final_pay.credited_years + ' year' + (detail.final_pay.credited_years === 1 ? '' : 's') + ')'"></dd></div>
        <div><dt>Age on last day</dt><dd x-text="detail.final_pay.age ?? 'Unknown'"></dd></div>
        <div><dt>Monthly pay</dt><dd x-text="peso(detail.final_pay.monthly_pay) + ' (' + peso(detail.final_pay.daily_wage) + ' x 26 days)'"></dd></div>
    </dl>

    <ul class="fp-lines">
        <template x-for="line in detail.final_pay.lines" :key="line.label">
            <li>
                <div><strong x-text="line.label"></strong><small x-text="line.rule"></small></div>
                <span x-text="peso(line.amount)"></span>
            </li>
        </template>
        <li class="fp-total"><strong>Total</strong><span x-text="peso(detail.final_pay.total)"></span></li>
    </ul>
    <p class="fp-note">Unpaid salary, the prorated 13th month pay and leave conversion are paid through payroll and are not included here.</p>
</section>
