#!/usr/bin/env bash
set -euo pipefail
 for s in tools/rc_c12_smoke.sh tools/rc_c13_smoke.sh tools/rc_c14_smoke.sh tools/rc_c15_smoke.sh tools/rc_c16_smoke.sh tools/rc_c17_smoke.sh tools/rc_c18_smoke.sh tools/rc_c19_smoke.sh; do echo "--> $s"; bash $s; done; echo 'RC3 OK'
