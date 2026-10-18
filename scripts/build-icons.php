<?php
// Compatibility entry point: all icons now derive from the single vector master.
passthru('node '.escapeshellarg(__DIR__.'/build-brand.mjs'), $status);
exit($status);
