<?php 
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; ?>

<pre class="decoda-code<?php if (!empty($lang)) { echo ' ' . $classPrefix . $lang; } ?>"<?php if (!empty($hl)) { printf(' %s="%s"', $highlightAttribute, $hl); } ?>><code><?php echo $content; ?></code></pre>