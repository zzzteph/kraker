using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Text.RegularExpressions;
using System.Threading;
using System.Threading.Tasks;
using Kracker.Base.Domain.Configuration;
using Kracker.Base.Services.Model.Jobs;
using Serilog;

namespace Kracker.Base.Domain.HashCat
{
    public class HashCatCommandExecutor
    {
        private static readonly Regex _rg = new("STATUS[ \t]*([4,6-8])[ \t]*", RegexOptions.IgnoreCase);
        private readonly ProcessStartInfo _startInfo;
        private readonly int _repeatedStringsLimit;
        private readonly int _silencePeriodLimit;
        private readonly object _sync;
        private readonly ILogger _logger;
        private readonly AbstractJob? _job;
        
        public CancellationTokenSource CancellationToken { get; }

        public HashCatCommandExecutor(string arguments,
            HashCatSettings config,
            ILogger logger,
            string workingDirectory,
            AbstractJob? job)
        {
            CancellationToken = new CancellationTokenSource();
            _logger = logger;
            _job = job;

            _startInfo = new ProcessStartInfo
            {
                FileName = Path.GetFileName(config.Path),
                UseShellExecute = false,
                RedirectStandardOutput = true,
                RedirectStandardError = true,
                RedirectStandardInput = true,
                CreateNoWindow = true,
                Arguments = arguments,
                WorkingDirectory = workingDirectory
            };

            _sync = new object();

            _silencePeriodLimit = config.SilencePeriodBeforeKill ?? 60;
            _repeatedStringsLimit = config.RepeatedStringsBeforeKill ?? 1000;


            _logger.Information($"Build a command for hashcat:{_startInfo.FileName} {_startInfo.Arguments}");
        }

        public async Task<ExecutionResult> Execute(bool waitNullReceiveOutput = false)
        {
            
            Timer? workTimer = null;
            var stopwatch = new Stopwatch();
            
            try
            {
                var output = new List<string>();
                var errors = new List<string>();
                var outputIsTheSame = 0;
                var isSuccessful = true;
                var wasKill = false;
                var taskEnd = false;

                using (var process = new Process
                {
                    StartInfo = _startInfo,
                    EnableRaisingEvents = true
                })
                {
                    var receiveNullData = new TaskCompletionSource<bool>();
                    var silencePeriod = TimeSpan.FromMinutes(_silencePeriodLimit);
                    workTimer = new Timer(o =>
                    {
                        if (taskEnd || wasKill)
                            return;

                        process.Kill();
                        wasKill = true;

                        _logger.Information(
                            $"Haven't been any output from hashcat for {_silencePeriodLimit} minutes. Kill the process for job: {_job}");
                    }, null, silencePeriod, TimeSpan.FromMilliseconds(-1));


                    process.OutputDataReceived += (s, ea) =>
                    {
                        if (!taskEnd)
                            workTimer.Change(silencePeriod, TimeSpan.FromMilliseconds(-1));

                        if (ea.Data == null)
                        {
                            receiveNullData.SetResult(true);
                            return;
                        }

                        outputIsTheSame = ea.Data == output.LastOrDefault() ? outputIsTheSame + 1 : 0;
                        output.Add(ea.Data);
                        _logger.Information($"Hashcat out: {ea.Data}");

                        if (outputIsTheSame > _repeatedStringsLimit)
                        {
                            _logger.Information($"Too long get the same output. Repeats number: {outputIsTheSame}");
                            isSuccessful = false;
                        }

                        if (CancellationToken.IsCancellationRequested)
                            isSuccessful = false;

                        if (!_rg.IsMatch(ea.Data) && isSuccessful)
                            return;

                        lock (_sync)
                        {
                            if (wasKill)
                                return;
                            try
                            {
                                _logger.Information("Kill hashcat");
                                var proc = s as Process;
                                proc.StandardInput.WriteLineAsync("q");

                                if (!proc.WaitForExit(1500))
                                    process.Kill();

                                wasKill = true;
                            }
                            catch (Exception e)
                            {
                                _logger.Information($"Get an exception during killing the process {e}");
                            }
                        }
                    };

                    process.ErrorDataReceived += (s, ea) =>
                    {
                        if (ea?.Data == null)
                            return;

                        errors.Add(ea.Data);
                        _logger.Information($"Hashcat err: {ea.Data}");
                    };

                    stopwatch.Start();
                    
                    var code = await RunProcessAsync(process);
                    
                    stopwatch.Stop();
                    taskEnd = true;
                    
                    if (waitNullReceiveOutput)
                        await receiveNullData.Task;
                    workTimer.Dispose();
                    return new ExecutionResult(code,
                        output,
                        errors,
                        isSuccessful,
                        null,
                        (long) stopwatch.Elapsed.TotalSeconds
                    );
                }
            }
            catch (Exception e)
            {
                
                stopwatch.Stop();
                workTimer?.Dispose();

                _logger.Error(e, $"Get an exception during hashcat working: {e}");
                return ExecutionResult.FromError((long) stopwatch.Elapsed.TotalSeconds, e.ToString());
            }
        }

        private Task<int> RunProcessAsync(Process process)
        {
            var tcs = new TaskCompletionSource<int>();

            process.Exited += (s, ea) =>
            {
                try
                {
                    var proc = s as Process;
                    tcs.SetResult(proc.ExitCode);
                }
                catch (Exception e)
                {
                    _logger.Error(e,"Exception during finishing of the process");
                    tcs.TrySetResult(-1);
                }
            };

            if (!process.Start()) 
                throw new InvalidOperationException("Could not start process: " + process);

            process.BeginOutputReadLine();
            process.BeginErrorReadLine();

            return tcs.Task;
        }
    }
}