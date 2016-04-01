function! Php7hostProvider(host) abort
  let hostscript = '/home/Xerkus/workspace/Xerkus/neovim_php7_host/bin/plugin-host.php'
  " Collect registered Python plugins into args
  let plugins = remote#host#PluginsForHost(a:host.name)
  if empty(plugins)
    " provide some nice info
  endif
  let args = []
  for plugin in plugins
    call add(args, plugin.path)
  endfor
  
  " add plugin path manually for now, remove after host can provide specs
  call add(args, expand("<sfile>:p:h") . "/php7plugin/rplugin/php7/plugin.php")

  try
    let channel_id = rpcstart(hostscript, args)
    if rpcrequest(channel_id, 'poll') == 'ok'
      return channel_id
    endif
  catch
    echomsg v:throwpoint
    echomsg v:exception
  endtry
  throw remote#host#LoadErrorForHost(a:host.orig_name,
        \ '$NVIM_PHP7_LOG_FILE')
endfunction

call remote#host#Register('php7', 'plugin.php',
      \ function('Php7hostProvider'))

set runtimepath^=/home/Xerkus/workspace/Xerkus/testrc/php7plugin

call remote#host#Require('php7')
