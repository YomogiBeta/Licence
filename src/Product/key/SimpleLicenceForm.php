<?php

namespace Product\key;

use pocketmine\form\Form;
use pocketmine\player\Player;

use Product\key\Licence;

class SimpleLicenceForm implements Form
{
    private String $licenseText;
    private Licence $ownerPluginBase;

    public function __construct(String $licenceText, Licence $ownerPluginBase)
    {
        $this->licenseText = $licenceText;
        $this->ownerPluginBase = $ownerPluginBase;
    }

    final public function jsonSerialize(): array
    {
        return [
            "type" => "form",
            "title" => "§lTerms of Service",
            "content" => $this->licenseText,
            "buttons" => [
                [
                    "text" => "§l同意する / Agree" //0
                ],
                [
                    "text" => "§l同意しない / Disagree" //1
                ]
            ]
        ];
    }

    final public function handleResponse(Player $player, $data) : void{

        /** ユーザーがフォームを閉じた場合 */
        if(is_null($data)){
            $player->sendForm($this);
            return;
        }

        /** ユーザーが利用規約に同意しない場合 */
        if($data === 1){
            $player ->kick("I'm sorry. If you do not agree to the terms of service, we can not use this server", false);
            return;
        }

        /** ユーザーが利用規約に同意する場合 */
        $this->ownerPluginBase->addAgreeUser($player->getName());
        $player ->sendMessage("§dYou have agreed to the terms of service");
	}
}
