<?php

declare(strict_types=1);

namespace DataLioness\ReplantingCrops;

use pocketmine\block\Crops;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\BlockBreakSound;

class ReplantingCrops extends PluginBase implements Listener {

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCropInteract(PlayerInteractEvent $event): void
    {
        $block = $event->getBlock();
        if($block instanceof Crops && $event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            if($block->getAge() >= $block::MAX_AGE){
                $event->cancel();

                $drops = $block->getDrops(VanillaItems::AIR());

                // Resetting the crop
                $block->setAge(0);
                $block->getPosition()->getWorld()->setBlock($block->getPosition(), $block);

                // Play sound
                $block->getPosition()->getWorld()->addSound($block->getPosition(), new BlockBreakSound($block));

                // Spawn particle
                $block->getPosition()->getWorld()->addParticle($block->getPosition(), new BlockBreakParticle($block));

                // Drop drop items
                foreach($drops as $drop){
                    $block->getPosition()->getWorld()->dropItem($block->getPosition(), $drop);
                }
            }
        }
    }

}
