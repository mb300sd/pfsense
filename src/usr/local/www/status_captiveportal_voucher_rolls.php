<?php
/*
	status_captiveportal_voucher_rolls.php

	Copyright (C) 2007 Marcel Wiget <mwiget@mac.com>.
	Copyright (C) 2013-2015 Electric Sheep Fencing, LP
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
/*
	pfSense_MODULE: captiveportal
*/

##|+PRIV
##|*IDENT=page-status-captiveportal-voucher-rolls
##|*NAME=Status: Captive portal Voucher Rolls page
##|*DESCR=Allow access to the 'Status: Captive portal Voucher Rolls' page.
##|*MATCH=status_captiveportal_voucher_rolls.php*
##|-PRIV

require("guiconfig.inc");
require("functions.inc");
require_once("filter.inc");
require("shaper.inc");
require("captiveportal.inc");
require_once("voucher.inc");

$cpzone = $_GET['zone'];
if (isset($_POST['zone'])) {
	$cpzone = $_POST['zone'];
}

if (empty($cpzone)) {
	header("Location: services_captiveportal_zones.php");
	exit;
}

if (!is_array($config['captiveportal'])) {
	$config['captiveportal'] = array();
}
$a_cp =& $config['captiveportal'];
$pgtitle = array(gettext("Status"), gettext("Captive portal"), gettext("Voucher Rolls"), $a_cp[$cpzone]['zone']);
$shortcut_section = "captiveportal-vouchers";

if (!is_array($config['voucher'][$cpzone]['roll'])) {
	$config['voucher'][$cpzone]['roll'] = array();
}

$a_roll = &$config['voucher'][$cpzone]['roll'];

include("head.inc");

$tab_array = array();
$tab_array[] = array(gettext("Active Users"), false, "status_captiveportal.php?zone={$cpzone}");
$tab_array[] = array(gettext("Active Vouchers"), false, "status_captiveportal_vouchers.php?zone={$cpzone}");
$tab_array[] = array(gettext("Voucher Rolls"), true, "status_captiveportal_voucher_rolls.php?zone={$cpzone}");
$tab_array[] = array(gettext("Test Vouchers"), false, "status_captiveportal_test.php?zone={$cpzone}");
$tab_array[] = array(gettext("Expire Vouchers"), false, "status_captiveportal_expire.php?zone={$cpzone}");
display_top_tabs($tab_array);
?>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
	    <thead>
    		<tr>
    			<th><?=gettext("Roll#"); ?></th>
    			<th><?=gettext("Minutes/Ticket"); ?></th>
    			<th><?=gettext("# of Tickets"); ?></th>
    			<th><?=gettext("Comment"); ?></th>
    			<th><?=gettext("used"); ?></th>
    			<th><?=gettext("active"); ?></th>
    			<th><?=gettext("ready"); ?></th>
    		</tr>
		</thead>
		<tbody>
<?php
			$voucherlck = lock("vouche{$cpzone}r");
			$i = 0;
			foreach($a_roll as $rollent):
				$used = voucher_used_count($rollent['number']);
				$active = count(voucher_read_active_db($rollent['number']),$rollent['minutes']);
				$ready = $rollent['count'] - $used;
				/* used also count active vouchers, remove them */
				$used = $used - $active;
?>
    		<tr>
    			<td>
    				<?=htmlspecialchars($rollent['number'])?>
    			</td>
    			<td>
    				<?=htmlspecialchars($rollent['minutes'])?>
    			</td>
    			<td>
    				<?=htmlspecialchars($rollent['count'])?>
    			</td>
    			<td>
    				<?=htmlspecialchars($rollent['comment'])?>
    			</td>
    			<td>
    				<?=htmlspecialchars($used)?>
    			</td>
    			<td>
    				<?=htmlspecialchars($active)?>
    			</td>
    			<td>
    				<?=htmlspecialchars($ready)?>
    			</td>
    		</tr>
<?php
				$i++;
			endforeach;

			unlock($voucherlck)?>
	    </tbody>
	</table>
</div>
<?php include("foot.inc");