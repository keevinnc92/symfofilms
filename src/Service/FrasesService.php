<?php 

namespace App\Service;

Class	FrasesService{
	public function getFraseAleatoria():string{

		$frases = [
			'No me acuerdo de olvidarte.',
			'Todos los hombres mueren, pero no todos han vivido.',
			'Hazlo o no lo hagas, pero no lo intentes.',
			'Eso desean los que viven en estos tiempos, pero no nos toca a nosotros decidir qué tiempo vivir, sólo podemos elegir qué hacer con el tiempo que se nos ha dado.',
			'Nunca permitas que nadie te haga sentir que no te mereces lo que quieres.',
			'Todos esos momentos se perderán en el tiempo como lágrimas en la lluvia.',
			'Houston, tenemos un problema.',
			'Siempre nos quedará París.',
			'Vamos a necesitar un barco más grande.',
			'Me he subido a mi mesa para recordarme que siempre hay que mirar las cosas de un modo diferente.',
			'No hay que regresar aquí nunca, porque nunca sería tan divertido.',
			'Estoy exactamente donde quiero estar.',
			'Solo hay una persona que puede decidir lo que voy a hacer, y soy yo mismo.',
			'No existen preguntas sin respuestas, solo preguntas mal formuladas.',
			'Aún hay vagos destellos de civilidad en este matadero salvaje que alguna vez fue la humanidad.',
			'Lo único que está entre tu meta y tú, es la historia que te sigues contando a ti mismo de por qué no puedes lograrla.',
			'El verdadero perdedor es aquél que tiene tanto miedo a no ganar que ni siquiera lo intenta.'
		];
		
		return $frases[array_rand($frases)];
	}
}