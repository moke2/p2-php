<?php
/*
    p2 -  �a������֌W�̏���
*/

require_once './p2util.class.php'; // p2�p�̃��[�e�B���e�B�N���X
require_once './filectl.class.php';

/**
 * �X����a������ɃZ�b�g����
 *
 * $set �́A0(����), 1(�ǉ�), top, up, down, bottom
 */
function setPal($host, $bbs, $key, $setpal)
{
    global $_conf;

    //==================================================================
    // key.idx ��ǂݍ���
    //==================================================================
    // idxfile�̃p�X�����߂�
    $datdir_host = P2Util::datdirOfHost($host);
    $idxfile = $datdir_host."/".$bbs."/".$key.".idx";

    // ����idx�f�[�^������Ȃ�ǂݍ���
    if ($lines = @file($idxfile)) {
        $l = rtrim($lines[0]);
        $data = explode('<>', $l);
    }

    //==================================================================
    // p2_palace.idx�ɏ�������
    //==================================================================
    $palace_idx = $_conf['pref_dir']. '/p2_palace.idx';

    //================================================
    // �ǂݍ���
    //================================================

    // p2_palace �t�@�C�����Ȃ���ΐ���
    FileCtl::make_datafile($palace_idx, $_conf['palace_perm']);

    // palace_idx �ǂݍ���
    $pallines = @file($palace_idx);

    //================================================
    // ����
    //================================================
    $neolines = array();
    $before_line_num = 0;
    
    // �ŏ��ɏd���v�f���폜���Ă���
    if (!empty($pallines)) {
        $i = -1;
        foreach ($pallines as $l) {
            $i++;
            $l = rtrim($l);
            $lar = explode('<>', $l);
            // �d�����
            if ($lar[1] == $key) {
                $before_line_num = $i;    // �ړ��O�̍s�ԍ����Z�b�g
                continue;
            // key�̂Ȃ����͕̂s���f�[�^�Ȃ̂ŃX�L�b�v
            } elseif (!$lar[1]) {
                continue;
            } else {
                $neolines[] = $l;
            }
        }
    }

    // �V�K�f�[�^�ݒ�
    if ($setpal) {
        $newdata = "$data[0]<>{$key}<>$data[2]<>$data[3]<>$data[4]<>$data[5]<>$data[6]<>$data[7]<>$data[8]<>$data[9]<>{$host}<>{$bbs}";
        include_once './getsetposlines.inc.php';
        $rec_lines = getSetPosLines($neolines, $newdata, $before_line_num, $setpal);
    } else {
        $rec_lines = $neolines;
    }
    
    $cont = '';
    if (!empty($rec_lines)) {
        foreach ($rec_lines as $l) {
            $cont .= $l."\n";
        }
    }
    
    // ��������
    if (FileCtl::file_write_contents($palace_idx, $cont) === false) {
        die('Error: cannot write file.');
    }
    
    return true;
}
?>