<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Files;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileFeaturesTest extends TestCase
{
    public function testFiles2(): void
    {
        // test info dict on default settings
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2');
        $info = $torrent->getRawData()['info'];

//        echo export_test_data($info);
        $this->assertEquals('be5a07b2ba3beb89e4b04132eb135e3f1d771bf6', $torrent->getInfoHash());
        $this->assertEquals(
            [
                'files' => [
                    [
                        'length' => 6621359,
                        'path' => ['dir1', 'file1.txt',],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'length' => 6621359,
                        'path' => ['dir2', 'file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['dir2', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['dir3', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6291456,
                        'path' => ['dir4', 'aligned.txt'],
                        'sha1' => base64_decode("8uHdutKp152UxbBUEr66/UNo/I0="),
                    ],
                    [
                        'length' => 6621335,
                        'path' => ['dir5', 'file3.txt'],
                        'sha1' => base64_decode("WW5Dv31hzse3rO95vQfVTk7M3lg="),
                    ],
                    [
                        'attr' => 'x',
                        'length' => 6621355,
                        'path' => ['dir6', 'exec.txt'],
                        'sha1' => base64_decode("PLesPfBgCmcfBdyu9k95eUh8sfs="),
                    ],
                ],
                'name' => 'files2',
                'piece length' => 524288,
                'pieces' => base64_decode(<<<PIECES
                    UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQncUHAAkG
                    2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QDM/8hpiYYegLm
                    NYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75OsxO499nbjEf7uzNnu+SV
                    o3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruWQcFLtKsJKHaFxXK+CHAjzDnmGafRpFsB4bSUzgp+0sQDXMUsrJSQLw/NDTm
                    Pn/bTnmGQHeBTgKmOmfD2xAzRH44urq87cHGS3rTl+0kX9B454fj+djA0Ql4IwaPDK1KLN86ZRCx3nK8SN3z1d1UDPUNbYc
                    3lmTP56S91AY8zPVy3fiYmlJg8SqEu8RX1Fj6Eui5l1sUq0hWI4yWpQAb2mKbQum/3eKi8GFijMbhHi6ohVt3ZxNuGaAn5R
                    8wJfygMlu2WAH/UyxBm2ImLUPmg5ymUyOM+iyq7hemxZabEhF6kr3W5zBwWhh4/eAn/gECBGIajasb9ifD0RGTtdrdDgQb6
                    lYawAJwPVnYXAfqhNdf0ZDGu/V4zJvykNJE0mLYJXruorPkMXTwmWGhT45YE5zVrslaF3ayAkaSqXFQFCgTwYVdA7oHBgch
                    B2mOo6K2XyV/Ja8HuQuhjjHSrp2rX61IJ8RQk9YyRld+9DCw2SLiRQ7IqV99lL2cBrVNJpr5r6/mr4M9WT1RHCy4Ka2jjEt
                    +DXtJIyZjwBmXv/nlL1/0PyRtYXVhvq3ZTAp/gt56UQ7pQBASePaQf5ZplVC9dO5OSmwXk18AuV0vZB+615jqxF56fAob9Q
                    7VWC7jt0AIhJtR/N59DLAgOTOTnzHiMQ4dFzlLSocSeoh+gi5NXrWn4g5OcYdukpL6A8pfqMDwewYZul01KJ0Lr4P/c1Pzy
                    3ZjOfd5ISYhIn2/3Of5MSd/j1rsz/bidGUixzHuqTjk94YhQUfeFTsbcz36JHaKB4nrjS2qAnq+n7on4JbeFh4PWgY5weTf
                    SwqALrIDBBSFWTiDxGrwbOK9LPiYMcUo7TLWaCwl4e+OvRrCpW1bNp5QAHWQ4yS5F9P1iv140i3PAb4CjeAGdqqyZqbfUN2
                    b8O72vPd1t6pxPqmcxQZ1tHdiZN01iGSHwTCk/gyJcfPh9ELQtnN6BYwC1POCGTTQ4qUh9V0w9fv4zUKxVYB6RLGJzlzUbB
                    vrcXxXePelOQfm0YNe35o2jqNZ/CwzkSFM3VAdMOo0dcM0BIujq/jBa5F677uVXvj5rrS0gyPgd1yv8vkDEA9C5p/3RSKO+
                    2F2LECpL2X0dtKU5yGriXadsDdx0Zkr7f3zgzPYnsJYIPJD5RGYrWOtxWWIiDdMU7SvHZKK+KiAfaPIQL/anaJdmsSd3NaT
                    nAu0/VrRX9+NB3JsldxvwBW2f4AlCOkO3mVB20yYFZi1QZSSAldR8PGTu5H3ScPeJGCMp9pRzaVRrtQsPlxv/x6SgX3PbSB
                    2a6FPUjg3smM7lfqqlOOA4XPG/hJPxvKO+MYWeVu9zoMt0TYPxYmMEwF9AAbtxGBiZmen+AiPqxIKbqPLlGSK1kZQ4oj6DN
                    NETQvJ6BWruBA7jp8CGRmEOQYOHn2fKrfMFleGdkEXbM6sEvS6GMHVP9NO4vRRm8vPnOry+zHnTumbuVCJaOHbSqGDziCH0
                    f/gZwP8iE/ioGfdMdvGpNh6wE3SBXENYRkEObnyzNCR8OYwej3jVwrA1vJquZNgGl24g7mcSeWx3CVDJQoBOdvL2uNv5k91
                    2NSqDsq148NC46Gg+bV9/FCzQxfgJkt34kRAgGWnIDgLRF1Tc34TIBE3N6gB6Ul3eTFgk/s25PSRFAYPD467EWXs/KggFeX
                    MHHDztpLCpOn9OvXzvrpsXHqAWXpFVyW91qVYtekTZKVe9LcaP9D0tAJjfzeATAcSZnl/w6dNq6dIKE0gfDg1++opAOwoUy
                    09Ivl0OrMK0zakrZIHjxD1D7XZyFquEtyoFncBAGoiemHgbjlbptBv2C0llg8SL6bgNsa2eTsB0Poa6Qdf0HTQA9Pxg+8E4
                    FnBBKIbmiq3sre29Cgg4BTOLCiv4a22cQgrwIXLHOHXVzwHj6flpCJMEZcT9zEEnIupHqMickRLbF27aB/qeAf1VLkNtHO0
                    SGuLrNX7IOfcMFBsSOmcEzxO4Qa1kc+f9y42rkssQgYWt54AgXGlnOMBZvmWVRn16K0=
                    PIECES),
            ],
            $info
        );
    }

    public function testExecutable(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', [
            'detectExec' => true,
        ]);

        $info = $torrent->getRawData()['info'];

        $xfile = [];
        foreach ($info['files'] as $file) {
            if ($file['path'] === ['dir6', 'exec.txt']) {
                $xfile = $file;
            }
        }

        self::assertStringContainsString('x', $xfile['attr']);
    }
}
