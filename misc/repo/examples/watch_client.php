<?php
// Minimal example client: emits events and tails them.
echo "Emit two events...\n";
passthru('php tools/watch_emit.php');
passthru('php tools/watch_emit.php __PATH__ tA user:1 editor doc:99');
echo "Tail (press Ctrl+C to stop)...\n";
passthru('php tools/watch_tail.php');
