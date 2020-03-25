<div class="mod_tool_bottrap">
    <form action="x" method="post">
        <div class="row">
            <div class="form-check col">
                <input type="checkbox" name=bottrap_enabled" id="bottrap_enabled"{if $settings.enabled=='Y'} checked="checked"{/if} />
                <label class="form-check-label" for="bottrap_enabled">
                    {translate('Enable BotTrap')}
                </label>
            </div>
            <div class="col">
                <div class="form-group row">
                    <label class="col-form-label col-sm-2" for="bottrap_route" title="{translate('path/route to catch bad bots')}">{translate('Protected Route')}</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="bottrap_route" value="{$settings.route}" />
                    </div>
                </div>
            </div>
        </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>IP</th>
                <th>{translate('Date')}</th>
                <th>{translate('UserAgent')}</th>
                <th>{translate('Visits')}</th>
                <th>{translate('Whitelisted')}</th>
            </tr>
        </thead>
{foreach $data item}
        <tr>
            <td>{$item.ip}</td>
            <td>{$item.date}</td>
            <td>{$item.ua}</td>
            <td>{$item.visits}</td>
            <td><input type="checkbox" name="item_{$item.id}_whitelisted" id="item_{$item.id}_whitelisted"{if $item.whitelisted=='Y'} checked="checked"{/if} /> </td>
        </tr>
{/foreach}
    </table>
</div>
