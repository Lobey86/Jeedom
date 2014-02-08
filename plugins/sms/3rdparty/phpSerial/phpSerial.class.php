<?php

/**
 * Based on: phpSerial (Rémy Sanchez <thenux@gmail.com>)
 *  http://www.phpclasses.org/package/3679-PHP-Communicate-with-a-serial-port.html
 * I've done a change in readport function, triggering only with a list of
 * valid outputs. Windows support of the original class has been removed
 *
 * THIS PROGRAM COMES WITH ABSOLUTELY NO WARANTIES !
 * USE IT AT YOUR OWN RISKS !
 *
 * @author Gonzalo Ayuso <gonzalo123@gmail.com>
 * @author Rémy Sanchez <thenux@gmail.com>
 *
 * @copyright under GPL 2 licence
 */
class phpSerial {

    const SERIAL_DEVICE_NOTSET = 0;
    const SERIAL_DEVICE_SET = 1;
    const SERIAL_DEVICE_OPENED = 2;

    private $_device = null;
    private $_dHandle = null;
    private $_dState = self::SERIAL_DEVICE_NOTSET;
    private $_buffer = "";
    private $_os = "";

    /**
     * Constructor. Perform some checks about the OS and setserial
     *
     * @return Sms_Serial
     */
    function __construct() {
        setlocale(LC_ALL, "en_US");

        $sysname = php_uname();

        if (substr($sysname, 0, 5) === "Linux") {
            $this->_os = "linux";

            if ($this->_exec("stty --version") === 0) {
                register_shutdown_function(array($this, "deviceClose"));
            } else {
                throw new Exception("No stty availible, unable to run.");
            }
        } else {
            throw new Exception("Host OS is must be linux, unable tu run.");
        }
    }

    /**
     * Device set function : used to set the device name/address.
     * -> linux : use the device address, like /dev/ttyS0
     *
     * @param string $device the name of the device to be used
     * @return bool
     */
    function deviceSet($device) {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            if ($this->_os === "linux") {
                if (preg_match("@^COM(\d+):?$@i", $device, $matches)) {
                    $device = "/dev/ttyS" . ($matches[1] - 1);
                }

                if ($this->_exec("stty -F " . $device) === 0) {
                    $this->_device = $device;
                    $this->_dState = self::SERIAL_DEVICE_SET;
                    return true;
                }
            }
            throw new Exception("Specified serial port is not valid");
        } else {
            throw new Exception("You must close your device before to set an other one");
        }
    }

    /**
     * Opens the device for reading and/or writing.
     *
     * @param string $mode Opening mode : same parameter as fopen()
     * @return bool
     */
    function deviceOpen($mode = "r+b") {
        if ($this->_dState === self::SERIAL_DEVICE_OPENED) {
            throw new Exception("The device is already opened");
        }

        if ($this->_dState === self::SERIAL_DEVICE_NOTSET) {
            throw new Exception("The device must be set before to be open");
        }

        if (!preg_match("@^[raw]\+?b?$@", $mode)) {
            throw new Exception("Invalid opening mode : " . $mode . ". Use fopen() modes.");
        }

        $this->_dHandle = @fopen($this->_device, $mode);

        if ($this->_dHandle !== false) {
            stream_set_blocking($this->_dHandle, 0);
            $this->_dState = self::SERIAL_DEVICE_OPENED;
            return true;
        }

        $this->_dHandle = null;
        throw new Exception("Unable to open the device");
    }

    /**
     * Closes the device
     *
     * @return bool
     */
    function deviceClose() {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            return true;
        }

        if (fclose($this->_dHandle)) {
            $this->_dHandle = null;
            $this->_dState = self::SERIAL_DEVICE_SET;
            return true;
        }

        throw new Exception("Unable to close the device");
    }

    //
    // OPEN/CLOSE DEVICE SECTION -- {STOP}
    //
    //
    // CONFIGURE SECTION -- {START}
    //
    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param int $rate the rate to set the port in
     * @return bool
     */
    function confBaudRate($rate) {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set the baud rate : the device is either not set or opened");
        }

        $validBauds = array(
            110 => 11,
            150 => 15,
            300 => 30,
            600 => 60,
            1200 => 12,
            2400 => 24,
            4800 => 48,
            9600 => 96,
            19200 => 19,
            38400 => 38400,
            57600 => 57600,
            115200 => 115200
        );

        if (isset($validBauds[$rate])) {
            if ($this->_os === "linux") {
                $ret = $this->_exec("stty -F " . $this->_device . " " . (int) $rate, $out);
            } else {
                return false;
            }

            if ($ret !== 0) {
                throw new Exception("Unable to set baud rate: " . $out[1]);
            }
        }
    }

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param string $parity one of the modes
     * @return bool
     */
    function confParity($parity) {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set parity : the device is either not set or opened");
        }

        $args = array(
            "none" => "-parenb",
            "odd" => "parenb parodd",
            "even" => "parenb -parodd",
        );

        if (!isset($args[$parity])) {
            throw new Exception("Parity mode not supported");
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $args[$parity], $out);
        }

        if ($ret === 0) {
            return true;
        }

        throw new Exception("Unable to set parity : " . $out[1]);
    }

    /**
     * Sets the length of a character.
     *
     * @param int $int length of a character (5 <= length <= 8)
     * @return bool
     */
    function confCharacterLength($int) {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set length of a character : the device is either not set or opened");
        }

        $int = (int) $int;
        if ($int < 5)
            $int = 5;
        elseif ($int > 8)
            $int = 8;

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " cs" . $int, $out);
        }

        if ($ret === 0) {
            return true;
        }
        throw new Exception("Unable to set character length : " . $out[1]);
    }

    /**
     * Sets the length of stop bits.
     *
     * @param float $length the length of a stop bit. It must be either 1,
     * 1.5 or 2. 1.5 is not supported under linux and on some computers.
     * @return bool
     */
    function confStopBits($length) {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set the length of a stop bit : the device is either not set or opened");
        }

        if ($length != 1 and $length != 2 and $length != 1.5 and !($length == 1.5 and $this->_os === "linux")) {
            throw new Exception("Specified stop bit length is invalid");
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . (($length == 1) ? "-" : "") . "cstopb", $out);
        }
        if ($ret === 0) {
            return true;
        }
        throw new Exception("Unable to set stop bit length : " . $out[1]);
    }

    /**
     * Configures the flow control
     *
     * @param string $mode Set the flow control mode. Availible modes :
     * 	-> "none" : no flow control
     * 	-> "rts/cts" : use RTS/CTS handshaking
     * 	-> "xon/xoff" : use XON/XOFF protocol
     * @return bool
     */
    function confFlowControl($mode) {
        if ($this->_dState !== self::SERIAL_DEVICE_SET) {
            throw new Exception("Unable to set flow control mode : the device is either not set or opened");
            return false;
        }

        $linuxModes = array(
            "none" => "clocal -crtscts -ixon -ixoff",
            "rts/cts" => "-clocal crtscts -ixon -ixoff",
            "xon/xoff" => "-clocal -crtscts ixon ixoff"
        );

        if ($mode !== "none" and $mode !== "rts/cts" and $mode !== "xon/xoff") {
            throw new Exception("Invalid flow control mode specified");
        }

        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $linuxModes[$mode], $out);
        }

        if ($ret === 0) {
            return true;
        } else {
            throw new Exception("Unable to set flow control : " . $out[1]);
        }
    }

    function sendParameters($param) {
        if ($this->_os === "linux") {
            $ret = $this->_exec("stty -F " . $this->_device . " " . $param, $out);
        }

        if ($ret === 0) {
            return true;
        } else {
            throw new Exception("Unable to set parameter " . $param . " : " . $out[1]);
        }
    }

    /**
     * Sets a setserial parameter (cf man setserial)
     * NO MORE USEFUL !
     * 	-> No longer supported
     * 	-> Only use it if you need it
     *
     * @param string $param parameter name
     * @param string $arg parameter value
     * @return bool
     */
    function setSetserialFlag($param, $arg = "") {
        if (!$this->_ckOpened())
            return false;

        $return = exec("setserial " . $this->_device . " " . $param . " " . $arg . " 2>&1");

        if ($return{0} === "I") {
            throw new Exception("setserial: Invalid flag");
        } elseif ($return{0} === "/") {
            throw new Exception("setserial: Error with device file");
        } else {
            return true;
        }
    }

    /**
     * Sends a string to the device
     *
     * @param string $str string to be sent to the device
     * @param float $waitForReply time to wait for the reply (in seconds)
     */
    function sendMessage($str, $waitForReply = 0.1) {
        $this->_buffer .= $str;
        $this->flush();

        usleep((int) ($waitForReply * 1000000));
    }

    private $_validOutputs = array();

    public function setValidOutputs($validOutputs) {
        $this->_validOutputs = $validOutputs;
    }

    /**
     * Reads the port until no new datas are availible, then return the content.
     *
     * @pararm int $count number of characters to be read (will stop before
     * 	if less characters are in the buffer)
     * @return string
     */
    function readPort() {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            throw new Exception("Device must be opened to read it");
        }
        if ($this->_os === "linux") {
            $last = null;
            $buffer = array();

            if ($this->_dHandle) {
                $_buffer = "";
                $startTime = getmicrotime();
                $continue = true;
                while (!in_array($last, $this->_validOutputs) && $continue) {
                    $bit = fread($this->_dHandle, 1);
                    if ($bit == "\n") {
                        $last = strtoupper(trim(strtoupper($_buffer)));
                        $buffer[] = $_buffer;
                        $_buffer = "";
                    } else {
                        $_buffer .= $bit;
                    }
                    if (round(getmicrotime() - $startTime, 3) > 10) {
                        $continue = false;;
                    }
                }
                return array($last, $buffer);
            }
        }
        return false;
    }

    /**
     * Flushes the output buffer
     *
     * @return bool
     */
    private function flush() {
        $this->_ckOpened();
        if (fwrite($this->_dHandle, $this->_buffer) !== false) {
            $this->_buffer = "";
            return true;
        }
        $this->_buffer = "";
        throw new Exception("Error while sending message");
    }

    private function _ckOpened() {
        if ($this->_dState !== self::SERIAL_DEVICE_OPENED) {
            throw new Exception("Device must be opened");
        }
        return true;
    }

    private function _ckClosed() {
        if ($this->_dState !== SERIAL_DEVICE_CLOSED) {
            throw new Exception("Device must be closed");
        }
        return true;
    }

    private function _exec($cmd, &$out = null) {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $proc = proc_open($cmd, $desc, $pipes);

        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $retVal = proc_close($proc);

        if (func_num_args() == 2)
            $out = array($ret, $err);
        return $retVal;
    }

}
