<?php
it('exposes health endpoint',function(){ $this->getJson('/api/health')->assertOk()->assertJsonPath('success',true);});
