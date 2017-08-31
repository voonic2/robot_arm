<?php
	class Robot {

		public $setup;
		
		public function __construct($setup) {
			$this->setup = $setup;
		}
	
		public function executeCommand($command) {
			$command_parts = explode(" ", $command);
			$from = $command_parts[0];
			$to = $command_parts[2];
			$a = $command_parts[1];
			$b = $command_parts[3];
			
			if($from == "move" && $to == "onto") {
				$this->move_onto((int)$a,(int)$b);
			} else if($from == "move" && $to == "over") {
				$this->move_over((int)$a,(int)$b);
			} else if($from == "pile" && $to == "onto") {
				$this->pile_onto((int)$a,(int)$b);
			} else if($from == "pile" && $to == "over") {
				$this->pile_over((int)$a,(int)$b);
			}
		}
		
		private function move_onto($a, $b) {
			// Get $ a and $b positions
			list($a_position, $b_position) = $this->get_initial_positions($a, $b);
			
			// Send all blocks on top of $a and $b to original positions
			$this->reset_positions($a, $a_position);
			$this->reset_positions($b, $b_position);
			
			// Move $a onto $b
			$this->setup->blocks[$a_position] = array_diff($this->setup->blocks[$a_position], array($a));
			$this->setup->blocks[$b_position] = array_merge($this->setup->blocks[$b_position], array($a));
		}
		
		private function move_over($a, $b) {
			// Get $ a and $b positions
			list($a_position, $b_position) = $this->get_initial_positions($a, $b);
			
			// Send all blocks on top of $a to original positions
			$this->reset_positions($a, $a_position);
			
			// Move $a over $b
			$this->setup->blocks[$a_position] = array_diff($this->setup->blocks[$a_position], array($a));
			$this->setup->blocks[$b_position] = array_merge($this->setup->blocks[$b_position], array($a));
		}
		
		private function pile_onto($a, $b) {
			// Get $ a and $b positions
			list($a_position, $b_position) = $this->get_initial_positions($a, $b);
			
			// Send all blocks on top of $b to original positions
			$this->reset_positions($b, $b_position);
			
			// Pile $a onto $b
			$blocks_on_top = $this->get_blocks_on_top($a, $a_position);
			$this->setup->blocks[$a_position] = array_diff($this->setup->blocks[$a_position], $blocks_on_top);
			$this->setup->blocks[$b_position] = array_merge($this->setup->blocks[$b_position], $blocks_on_top);
		}
		
		private function pile_over($a, $b) {
			// Get $ a and $b positions
			list($a_position, $b_position) = $this->get_initial_positions($a, $b);
			
			// Pile $a onto $b
			$blocks_on_top = $this->get_blocks_on_top($a, $a_position);
			$this->setup->blocks[$a_position] = array_diff($this->setup->blocks[$a_position], $blocks_on_top);
			$this->setup->blocks[$b_position] = array_merge($this->setup->blocks[$b_position], $blocks_on_top);
		}
		
		
		// Helper functions
		
		private function get_initial_positions($a, $b) {
			$a_position = null;
			$b_position = null;
			foreach($this->setup->blocks as $i => $block) {
				if(in_array($a, $block)) {
					$a_position = $i;
				}
				if(in_array($b, $block)) {
					$b_position = $i;
				}
			}
			return array($a_position, $b_position);
		}
		
		private function reset_positions($n, $position) {
			$found_n = false;
			foreach($this->setup->blocks[$position] as $block) {
				if($found_n) {
					$this->setup->blocks[$block] = array(block);
				} else if($block == $n) {
					$found_n = true;
				}
			}
		}
		
		private function get_blocks_on_top($n, $n_position) {
			$found_n = false;
			$blocks_on_top = array();
			foreach($this->setup->blocks[$n_position] as $block) {
				if($block == $n) {
					$found_n = true;
				}
				
				if($found_n) {
					$blocks_on_top[] = $block;
				}
			}
			return $blocks_on_top;
		}

	}
	
	class Setup {
		
		public $blocks = array();
		public $input = array();
		
		public function set_blocks($n) {
			for($i=0; $i<$n; $i++) {
				$this->blocks[$i] = array($i);
			}
		}
		
		public function set_input() {
			$file = fopen("input.txt","r");
			while(! feof($file))
			{
				$this->input[] = fgets($file);
			}
			fclose($file);
		}
		
	}
	
	$setup = new Setup;
	$setup->set_input();
	$input = $setup->input;
	$setup->set_blocks($input[0]);
	
	$robot = new Robot($setup);
	
	foreach($input as $command) {
		if(strpos($command, "move") !== false || strpos($command, "pile") !== false) {
			$robot->executeCommand($command);
		}
	}
	
	print_r($setup->blocks);